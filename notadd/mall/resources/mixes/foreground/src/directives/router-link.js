import router from '../router';

export default {
    bind(el, binding) {
        const element = el;
        element.onclick = () => {
            router.push(binding.value);
        };
    },
};
