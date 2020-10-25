import {
    mixinAxios,
    mixinComponent,
} from '../mixes/injection';

const injection = {};

function install(Vue) {
    mixinAxios(injection, Vue);
    mixinComponent(injection, Vue);
}

export default Object.assign(injection, {
    install,
});