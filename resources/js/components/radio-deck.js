function normalizeSearchString(value) {
    return (value ?? '')
        .normalize('NFD')
        .replace(/\p{M}/gu, '')
        .toLowerCase()
}

export default function radioDeckFormComponent() {
    return {
        search: '',

        visibleOptionsCount: null,

        areAllCheckboxesChecked: false,

        init() {
            this.$nextTick(() => this.refresh())

            this.$watch('search', () => this.refresh())
        },

        getOptions() {
            return Array.from(
                this.$root.querySelectorAll('.fi-fo-radio-deck-option'),
            )
        },

        getVisibleOptions() {
            return this.getOptions().filter((option) =>
                this.matchesSearch(option),
            )
        },

        refresh() {
            const visibleOptions = this.getVisibleOptions()

            this.visibleOptionsCount = visibleOptions.length

            this.areAllCheckboxesChecked =
                visibleOptions.length > 0 &&
                visibleOptions.every((option) =>
                    option.querySelector(
                        'input[type=checkbox]:checked, input[type=checkbox]:disabled',
                    ),
                )
        },

        toggleAllCheckboxes() {
            this.refresh()

            const shouldCheck = !this.areAllCheckboxesChecked

            this.getVisibleOptions().forEach((option) => {
                const checkbox = option.querySelector('input[type=checkbox]')

                if (!checkbox || checkbox.disabled) {
                    return
                }

                if (checkbox.checked === shouldCheck) {
                    return
                }

                checkbox.checked = shouldCheck
                checkbox.dispatchEvent(new Event('change', { bubbles: true }))
            })

            this.areAllCheckboxesChecked = shouldCheck
        },

        matchesSearch(option) {
            const search = normalizeSearchString(this.search)

            if (search === '') {
                return true
            }

            return ['.fi-fo-radio-deck-label', '.fi-fo-radio-deck-description']
                .map((selector) =>
                    normalizeSearchString(
                        option.querySelector(selector)?.textContent,
                    ),
                )
                .some((text) => text.includes(search))
        },
    }
}
