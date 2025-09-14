
<script>
  import { ref } from 'vue';
  import { register } from '../../services/authService';
  import { makeUserAdmin } from '../../services/adminService';
  import { useRouter } from 'vue-router';
  import { useUserStore } from '../../store/user';
  import { useConfigStore } from '../../store/config';
  import RegisterForm from '../forms/UserForm.vue';

  export default {
    components: {
      RegisterForm,
    },

    setup(props) {
      const router = useRouter();
      const userStore = useUserStore();
      const configStore = useConfigStore();
      
      const handleRegister = async (formData, setError) => {
        try {
          let response;
          response = await register(formData);
          localStorage.setItem('token', response.token);
          userStore.setUser(response.user);
          
          if (configStore.mustConfigure) {
            response = await makeUserAdmin({'username': response.user.username});
          }

          router.push('/dashboard');
        } catch (err) {
          setError(err.message);
        }
      };

      return { handleRegister, configStore };
    },
  };
</script>

<template>
  <div class="container register-page">
    <div class="box">
      <div class="left-child information-container">
        <div class="logo-container">
          <div class="logo"></div>
        </div>
        <div class="information">
          <h1 v-if="configStore.mustConfigure">Let's set up this application</h1>
          <p v-if="configStore.mustConfigure">
            Looks like you don't have an admin in the page, let's set up one
          </p>
          <h1 v-else>Register new user</h1>
        </div>
      </div>

      <div class="right-child signup-container">
        <RegisterForm 
          :mustConfigure="configStore.mustConfigure"
          :isRegister="true"
          :onSubmit="handleRegister"
        />
      </div>
    </div>
  </div>
</template>

<style lang="scss">
  .register-page {
    .box {
      grid-template-columns: 2fr 3fr;

      .information-container {
        display: flex;
        flex-direction: column;
        gap: $gap;
        padding: 20px;
        width: 100%;
        background-color: $primary;
        height: 100%;
        color: $white;
        display: grid;

        .information {
          display: flex;
          flex-direction: column;
          gap: $gap;
        }
      }

      .signup-container {
        padding: 20px;
        width: 100%;
      }
    }
  }
</style>
