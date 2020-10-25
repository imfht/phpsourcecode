<script>
    export default {
        props: {
            pageCount: {
                type: Number,
                required: true,
            },
            clickHandler: {
                type: Function,
                default: () => {
                },
            },
            pageRange: {
                type: Number,
                default: 3,
            },
            marginPages: {
                type: Number,
                default: 1,
            },
            prevText: {
                type: String,
                default: 'Prev',
            },
            nextText: {
                type: String,
                default: 'Next',
            },
            containerClass: {
                type: String,
            },
            pageClass: {
                type: String,
            },
            pageLinkClass: {
                type: String,
            },
            prevClass: {
                type: String,
            },
            prevLinkClass: {
                type: String,
            },
            nextClass: {
                type: String,
            },
            nextLinkClass: {
                type: String,
            },
        },
        data() {
            return {
                selected: 0,
            };
        },
        computed: {
            pages() {
                const items = {};
                if (this.pageCount <= this.pageRange) {
                    for (let id = 0; id < this.pageCount; id++) {
                        items[id] = {
                            index: id,
                            content: id + 1,
                            selected: id === this.selected,
                        };
                    }
                } else {
                    let leftPart = this.pageRange / 2;
                    let rightPart = this.pageRange - leftPart;
                    if (this.selected < leftPart) {
                        leftPart = this.selected;
                        rightPart = this.pageRange - leftPart;
                    } else if (this.selected > this.pageCount - (this.pageRange / 2)) {
                        rightPart = this.pageCount - this.selected;
                        leftPart = this.pageRange - rightPart;
                    }
                    for (let id = 0; id < this.pageCount; id += 1) {
                        const page = {
                            index: id,
                            content: id + 1,
                            selected: id === this.selected,
                        };
                        if (id <= this.marginPages - 1 || id >= this.pageCount - this.marginPages) {
                            items[id] = page;
                        }
                        const breakView = {
                            content: '...',
                            disabled: true,
                        };
                        const left = this.selected - leftPart;
                        if (left > this.marginPages && items[this.marginPages] !== breakView) {
                            items[this.marginPages] = breakView;
                        }
                        const count = this.pageCount - this.marginPages - 1;
                        const right = this.selected + rightPart;
                        const what = items[this.pageCount - this.marginPages - 1] !== breakView;
                        if (right < count && what) {
                            items[this.pageCount - this.marginPages - 1] = breakView;
                        }
                        const overCount = ((this.selected + rightPart) - this.pageCount) + 1;
                        if (overCount > 0 && id === this.selected - leftPart - overCount) {
                            items[id] = page;
                        }
                        if ((id >= this.selected - leftPart) && (id <= this.selected + rightPart)) {
                            items[id] = page;
                        }
                    }
                }
                return items;
            },
        },
        methods: {
            handlePageSelected(selected) {
                if (this.selected === selected) return;
                this.selected = selected;
                this.clickHandler(this.selected + 1);
            },
            prevPage() {
                if (this.selected <= 0) return;
                this.selected -= 1;
                this.clickHandler(this.selected + 1);
            },
            nextPage() {
                if (this.selected >= this.pageCount - 1) return;
                this.selected += 1;
                this.clickHandler(this.selected + 1);
            },
        },
    };
</script>
<style>
    .pagination > li > a {
        background: #ffffff;
        border-color: transparent;
        cursor: pointer;
        margin: 0 5px;
    }
    .pagination > li:first-child > a,
    .pagination > li:last-child > a {
        border-color: #cccccc;
        color: #cccccc;
        border-radius: 0;
    }

    .pagination > .disabled > span,
    .pagination > .disabled > span:hover,
    .pagination > .disabled > span:focus,
    .pagination > .disabled > a,
    .pagination > .disabled > a:hover,
    .pagination > .disabled > a:focus {
        border: none;
    }

    .pagination > li:first-child > a,
    .pagination > li:last-child > a {
        border-color: #cccccc;
        color: #cccccc;
    }

    .pagination > li:first-child > a {
        margin-left: 0;
        margin-right: 10px;
    }

    .pagination > li:last-child > a {
        margin-left: 10px;
        margin-right: 0;
    }

    .pagination > li > a:hover,
    .pagination > li.active > a {
        background: #de2634;
        border-color: #de2634;
        color: #ffffff;
        margin-bottom: 1px;
        margin-top: 1px;
        padding-bottom: 5px;
        padding-top: 5px;
    }
</style>
<template>
    <ul :class="containerClass">
        <li :class="prevClass">
            <a @click="prevPage()" :class="prevLinkClass">
                {{ prevText }}
            </a>
        </li>
        <li v-for="page in pages" :class="[{ active: page.selected, disabled: page.disabled }, pageClass]">
            <a v-if="page.disabled" :class="pageLinkClass">{{ page.content }}</a>
            <a v-else @click="handlePageSelected(page.index)" :class="pageLinkClass">{{ page.content }}</a>
        </li>
        <li :class="nextClass">
            <a @click="nextPage()" :class="nextLinkClass">
                {{ nextText }}
            </a>
        </li>
    </ul>
</template>