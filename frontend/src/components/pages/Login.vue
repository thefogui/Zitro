<script>
  import { ref } from 'vue';
  import { login } from '../../services/authService';
  import { useUserStore } from '../../store/user';
  import { useRouter } from 'vue-router';

  export default {
    setup() {
      const router = useRouter();
      const userStore = useUserStore();

      const form = ref({
        username: '',
        password: '',
      });

      const showError = ref(false);

      const handleLogin = async () => {
        try {
          const response = await login(form.value);
          localStorage.setItem('token', response.token);
          userStore.setUser(response.user);
          router.push('/dashboard');
        } catch (err) {
          console.error(err);
          showError.value = true;
        }
      }


      return { form, handleLogin, showError }
    },
  }
</script>

<template>
  <div class="container login-page">
    <div class="box">
      <div class="welcome-container">
        <h1>Welcome back!</h1>
        <p>Enter your account to start</p>
      </div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="username"> Username <span class="required">*</span></label>
          <input class="form-control" id="username" name="username" placeholder="username" v-model="form.username" required />
        </div>

        <div class="form-group">
          <label for="password"> Password <span class="required">*</span></label>
          <input type="password" class="form-control" id="password" name="password" placeholder="password" v-model="form.password" required />

          <span v-if="showError" class="form-validation-error">Username or password provided can not be verified</span>
        </div>

        <div class="form-footer">
          <div class="submit-button-container">
            <button type="submit" class="btn btn-primary">Log In</button>
          </div>

          <div class="login-link-container">
            <router-link class="link" to="/register">
              Need an account? Register
            </router-link>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style lang="scss">
  .login-page {
    .box {
      width: 400px;
      padding: 20px;
      gap: 20px;

      .welcome-container {
        display: grid;
        place-items: center;
        gap: $gap;
      }
    }
  }
</style>
