import uFuzzy from '@leeoniya/ufuzzy'

// Shared across every picker on the page. Fields with the same sets + scope
// share one index download; SVG markup is shared by icon id across all fields.
const indexCache = new Map()
const svgCache = new Map()

export default function iconPickerComponent({
                                                key,
                                                state,
                                                displayName,
                                                isDropdown,
                                                shouldCloseOnSelect,
                                                token,
                                                cacheKey,
                                                indexUrl,
                                                svgUrl,
                                            }) {
    const fuzzy = new uFuzzy({intraMode: 1})

    // Set-filtered view of the index the fuzzy search runs over.
    let hayIcons = []
    let haystack = []

    let svgQueue = []
    let svgFlushId = null

    return {
        state,
        displayName,
        isDropdown,
        shouldCloseOnSelect,
        dropdownOpen: false,
        set: null,
        sets: [],
        icons: [],
        search: '',
        results: [],
        resultsVisible: [],
        resultsPerPage: 50,
        resultsIndex: 0,
        isLoading: false,
        loaded: false,

        init() {
            // Inline pickers are visible right away; dropdowns load on open.
            if (! this.isDropdown) {
                this.loadIcons()
            }

            this.$wire.on(`custom-icon-uploaded::${key}`, (icon) => {
                this.displayName = icon.label
                this.set = null
                indexCache.delete(cacheKey)
                this.loadIcons()
            })
        },

        async loadIcons() {
            this.isLoading = true

            try {
                if (! indexCache.has(cacheKey)) {
                    indexCache.set(cacheKey, this.fetchIndex())
                }

                const index = await indexCache.get(cacheKey).catch((error) => {
                    indexCache.delete(cacheKey)
                    throw error
                })

                this.sets = index.sets
                this.icons = index.icons
                this.loaded = true
                this.rebuildHaystack()
                this.applySearch()
            } catch (error) {
                console.error('Icon picker: failed to load the icon index.', error)
                this.icons = []
                this.results = []
                this.resultsVisible = []
            } finally {
                this.isLoading = false
            }
        },

        async fetchIndex() {
            const url = new URL(indexUrl, window.location.origin)
            url.searchParams.set('token', token)

            const response = await fetch(url, {headers: {Accept: 'application/json'}})

            if (! response.ok) {
                throw new Error(`Icon index request failed (${response.status}).`)
            }

            const data = await response.json()

            return {
                sets: data.sets,
                icons: data.icons.map(([id, label, setIndex, custom]) => ({
                    id,
                    label,
                    set: data.sets[setIndex]?.id ?? null,
                    custom: Boolean(custom),
                })),
            }
        },

        rebuildHaystack() {
            hayIcons = this.set
                ? this.icons.filter((icon) => icon.set === this.set)
                : this.icons
            haystack = hayIcons.map((icon) => icon.id)
        },

        applySearch() {
            const needle = this.search.trim()

            if (! needle.length) {
                this.results = hayIcons
            } else {
                const [idxs, info, order] = fuzzy.search(haystack, needle, 1)

                if (idxs === null) {
                    this.results = hayIcons
                } else if (info && order) {
                    this.results = order.map((i) => hayIcons[info.idx[i]])
                } else {
                    this.results = idxs.map((i) => hayIcons[i])
                }
            }

            this.resultsVisible = []
            this.resultsIndex = 0
            this.addSearchResultsChunk()
        },

        addSearchResultsChunk() {
            const chunk = this.resultsIndex === 0 ? 100 : this.resultsPerPage
            const endIndex = Math.min(this.resultsIndex + chunk, this.results.length)

            this.resultsVisible.push(...this.results.slice(this.resultsIndex, endIndex))
            this.resultsIndex = endIndex
        },

        afterSetUpdated() {
            this.rebuildHaystack()
            this.applySearch()
        },

        setSelect: {
            ['x-on:change'](event) {
                const value = event.target.value
                this.set = value ? value : null

                this.afterSetUpdated()
            }
        },

        searchInput: {
            ['x-on:input.debounce.100ms'](event) {
                this.search = event.target.value
                this.applySearch()
            },
        },

        dropdownTrigger: {
            ['x-on:click.prevent']() {
                this.dropdownOpen = true

                if (! this.loaded && ! this.isLoading) {
                    this.loadIcons()
                }
            }
        },

        dropdownMenu: {
            ['x-show']() {
                return ! this.isDropdown || this.dropdownOpen
            },
            ['x-on:click.outside']() {
                this.dropdownOpen = false
            }
        },

        // Tiles call this as they scroll into view; requests are collected for
        // 50ms and resolved with batched fetches instead of one call per icon.
        setElementIcon(element, id, after = null) {
            if (! id) {
                element.innerHTML = ''
                after?.()

                return
            }

            if (svgCache.has(id)) {
                element.innerHTML = svgCache.get(id) ?? ''
                after?.()

                return
            }

            svgQueue.push({id, element, after})

            if (! svgFlushId) {
                svgFlushId = setTimeout(() => this.flushSvgQueue(), 50)
            }
        },

        async flushSvgQueue() {
            svgFlushId = null

            const queue = svgQueue
            svgQueue = []

            const ids = [...new Set(queue.map((entry) => entry.id))]
                .filter((id) => ! svgCache.has(id))

            // The server caps a batch at 50 ids.
            for (let start = 0; start < ids.length; start += 50) {
                const chunk = ids.slice(start, start + 50)

                try {
                    const url = new URL(svgUrl, window.location.origin)
                    url.searchParams.set('token', token)
                    chunk.forEach((id) => url.searchParams.append('ids[]', id))

                    const response = await fetch(url, {headers: {Accept: 'application/json'}})

                    if (! response.ok) {
                        continue
                    }

                    const svgs = (await response.json()).svgs ?? {}

                    // Nulls are cached too - the server refused the icon, and
                    // asking again will not change its mind.
                    chunk.forEach((id) => svgCache.set(id, svgs[id] ?? null))
                } catch (error) {
                    console.error('Icon picker: failed to load icon svgs.', error)
                }
            }

            queue.forEach(({id, element, after}) => {
                element.innerHTML = svgCache.get(id) ?? ''
                after?.()
            })
        },

        updateState(icon) {
            if (icon) {
                this.state = icon.id
                this.displayName = icon.label
                if (this.shouldCloseOnSelect) {
                    this.$nextTick(() => this.dropdownOpen = false)
                }
            } else {
                this.state = null
                this.displayName = null
            }
        }
    }
}
