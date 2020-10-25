import articleManager from './components/dashboard/articleManager.vue';
import pubAnalytics from './components/dashboard/pubAnalytics.vue';

const routes = [
    { path: '/', redirect: '/pub_analytics' },
    { path: '/article_manager', component: articleManager },
    { path: '/pub_analytics', component: pubAnalytics }
];

export default routes;
