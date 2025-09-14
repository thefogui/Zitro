import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/pages/Login.vue';
import Register from '../components/pages/Register.vue';
import Dashboard from '../components/pages/Dashboard.vue';
import { useConfigStore } from '../store/config';

const routes = [
  { path: '/login', name: 'Login', component: Login, meta: { guestOnly: true } },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guestOnly: true },
  },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard}
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const configStore = useConfigStore();
  const mustConfigure = await configStore.fetchMustConfigure();
  const token = localStorage.getItem('token');
  
  if (mustConfigure) {
    if (to.name !== 'Register') {
      return next({ name: 'Register' });
    }
    return next();
  } else {
    if (to.meta.guestOnly) {
      if(token) {
        return next({ name: 'Dashboard' }); 
      }
      return next();
    } else {
      if (token) {
        return next(); 
      }
      return next({ name: 'Login' }); 
    }
  }
});

export default router;
