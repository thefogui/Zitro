<script>
  import { ref, onMounted, watch } from 'vue';
  import { getDepartments } from '../../services/departmentService';
  import { getPositions } from '../../services/companyPositionService';

  export default {
    name: 'UserForm',

    props: {
      mustConfigure: {
        type: Boolean,
        default: false
      },
      isRegister: {
        type: Boolean,
        default: false,
        required: true,
      },
      onSuccess: {
        type: Function,
        default: null
      },
      onSubmit: {
        type: Function,
        required: true
      },
      initialData: {
        type: Object,
        default: null
      },
    },

    setup(props) {
      const form = ref({
        username: '',
        firstname: '',
        lastname: '',
        department: '',
        position: '',
        email: '',
        password: '',
      });

      const departments = ref([]);
      const positions = ref([]);
      const showError = ref('');
      const submitButtonText = ref('');
      const isShowLoginLink = ref(false);

      if (props.mustConfigure) {
          submitButtonText.value = 'Create Admin';
      } else {
          if (props.isRegister) {
              submitButtonText.value = 'Register user';
              isShowLoginLink.value = true;
          }
          if (props.initialData) {
            submitButtonText.value = 'Update user';
          } else {
            submitButtonText.value = 'Create new user';
          }
      }

      onMounted(async () => {
        try {
          departments.value = await getDepartments();
          positions.value = await getPositions();
        } catch (err) {
          console.error('Error cargando departamentos/positions:', err);
        }
      });

      watch(() => props.initialData, (val) => {
        if (val) {
          form.value = { 
            ...val, 
            department: val?.department?.name || '', 
            position: val?.position?.name || '', 
            password: '' 
          }
        }
        
      }, { immediate: true });

      const handleSubmit = () => {
        if (props.onSubmit) {
          props.onSubmit(form.value, (errorMsg) => {
            if (errorMsg == 422) {
              showError.value = "The email needs to have our company extension, '@company.com'";
            } else if (errorMsg == 23000) {
              showError.value = 'User already exists';
            } else {
              showError.value = 'There was an error';
            }
          });
        }
      };

      return { form, handleSubmit, departments, positions, submitButtonText, isShowLoginLink, showError, props }
    }
  }
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <!-- username -->
    <div class="form-group">
        <label for="username">Username <span class="required">*</span></label>
        <input
            v-model="form.username"
            id="username"
            name="username"
            class="form-control"
            required
        />
    </div>

    <!-- firstname -->
    <div class="form-group">
        <label for="firstname">First name <span class="required">*</span></label>
        <input
            v-model="form.firstname"
            id="firstname"
            name="firstname"
            class="form-control"
            required
        />
    </div>

    <!-- lastname -->
    <div class="form-group">
        <label for="lastname">Last name</label>
        <input
            v-model="form.lastname"
            id="lastname"
            name="lastname"
            class="form-control"
            required
        />
    </div>

    <!-- department -->
    <div v-if="props.mustConfigure" class="form-group">
        <label for="department">Department</label>
        <input
            v-model="form.department"
            id="department"
            name="department"
            class="form-control"
        />
    </div>

    <div v-else class="form-group">
        <label for="department">Department</label>
        <select
            v-model="form.department"
            id="department"
            name="department"
            class="form-control"
        >
            <option value="">Select department</option>
            <option 
            v-for="dept in departments" 
            :key="dept.id" 
            :value="dept.name"
            >
            {{ dept.name }}
            </option>
        </select>
    </div>

    <!-- position -->
    <div v-if="props.mustConfigure" class="form-group">
    <label for="position">Position</label>
    <input
        v-model="form.position"
        id="position"
        name="position"
        class="form-control"
    />
    </div>

    <div v-else class="form-group">
        <label for="position">Position</label>
        <select
            v-model="form.position"
            id="position"
            name="position"
            class="form-control"
        >
            <option value="">Select position</option>
            <option 
            v-for="pos in positions"
            :key="pos.id"
            :value="pos.name"
            >
            {{ pos.name }}
            </option>
        </select>
    </div>

    <!-- email -->
    <div class="form-group">
    <label for="email">Email <span class="required">*</span></label>
    <input
        v-model="form.email"
        id="email"
        name="email"
        class="form-control"
        required
    />
    <span>Your email needs to have our company extension, '@company.com'</span>
    </div>

    <!-- password -->
    <div class="form-group">
        <label for="password">Password <span class="required">*</span></label>
        <input
            type="password"
            v-model="form.password"
            id="password"
            name="password"
            class="form-control"
            required
        />
    </div>

    <span v-if="showError" class="form-validation-error">
        {{ showError }}
    </span>

    <div class="form-footer">
        <button type="submit" class="btn btn-primary">
            {{ submitButtonText }}
        </button>

        <div v-if="isShowLoginLink" class="login-link-container">
            <router-link class="link" to="/login">
                Already have an account? Login
            </router-link>
        </div>
    </div>
  </form>
</template>
