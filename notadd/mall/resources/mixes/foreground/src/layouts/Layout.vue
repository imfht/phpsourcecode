<script>
    import HeaderBar from './HeaderBar.vue';
    import FooterBar from './FooterBar.vue';
    import SplinLine from '../components/SplinLine.vue';
    import LogoModal from './LogoModal.vue';
    import ContentNav from './ContentNav.vue';
    import FooterContent from './FooterContent.vue';
    import RightSide from './RightSide.vue';

    export default {
        components: {
            ContentNav,
            FooterBar,
            FooterContent,
            HeaderBar,
            LogoModal,
            SplinLine,
            RightSide,
        },
        computed: {
            loading() {
                return this.$store.state.loading;
            },
        },
    };
</script>
<template>
    <div class="layout">
        <header-bar></header-bar>
        <logo-modal></logo-modal>
        <content-nav></content-nav>
        <splin-line v-show="loading"></splin-line>
        <div class="content">
            <transition name="fade">
                <router-view></router-view>
            </transition>
        </div>
        <right-side></right-side>
        <footer-content></footer-content>
        <footer-bar></footer-bar>
    </div>
</template>
