<template>
    <div ref="modal" class="address-modal modal fade background-darken" tabindex="-1" role="dialog"
         :class="{in:isOpen,show:isShow}" @click.self="close()" @keyup.esc="close()">
        <div class="modal-dialog" role="document">
            <div class="address-content">
                <div class="address-header">
                    <slot name="title">Modal</slot>
                </div>
                <div class="address-body">
                    <slot name="body">Body</slot>
                </div>
                <div class="address-footer">
                    <div class="signup-form-group">
                        <slot name="save_address"></slot>
                        <button type="button" class="close-btn" @click="close()">取消</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                isOpen: false,
                isShow: false,
            };
        },
        methods: {
            open() {
                const self = this;
                self.isShow = true;
                self.$nextTick(() => {
                    self.isOpen = true;
                    self.$refs.modal.focus();
                });
            },
            close() {
                const self = this;
                self.isOpen = false;
                self.$nextTick(() => {
                    setTimeout(() => {
                        self.isShow = false;
                    }, 500);
                });
            },
        },
    };
</script>