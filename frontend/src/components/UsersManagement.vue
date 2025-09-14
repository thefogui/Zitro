<script>
  import { ref, onMounted } from 'vue';
  import EasyDataTable from 'vue3-easy-data-table';
  import 'vue3-easy-data-table/dist/style.css';
  import { getUsers, deleteUser, createUser, updateUser, getUser } from '../services/userService';
  import UserForm from './forms/UserForm.vue';

  export default {
    name: 'UsersManagement',

    components: {
      EasyDataTable,
      UserForm,
    },

    setup() {
      const users = ref([]);
      const headers = [
        { text: 'ID', value: 'id' },
        { text: 'Username', value: 'username' },
        { text: 'Email', value: 'email' },
        { text: 'First Name', value: 'firstname' },
        { text: 'Last Name', value: 'lastname' },
        { text: 'Actions', value: 'actions' }
      ];
      const showUserForm = ref(false);
      const editingUser = ref(null);

      const fetchUsers = async () => {
        try {
          users.value = await getUsers();
        } catch (err) {
          console.error('Error fetching users:', err);
        }
      };

      const handleDeleteUser = async (user) => {
        await deleteUser(user.id);
        fetchUsers();
      };

      const editUser = async (user) => {
        try {
          user = await getUser(user.id);
          editingUser.value = { ...user };
          openUserForm();
        } catch (err) {
          console.error('Error fetching user:', err);
        }
      };

      onMounted(fetchUsers);

      const openUserForm = () => {
        showUserForm.value = true;
      };

      const closeUserForm = () => {
        showUserForm.value = false;
        editingUser.value = null;
        fetchUsers();
      };

      const handleSubmit = async (formData, setError) => {
        try {
          if (editingUser.value) {
            await updateUser(editingUser.value.id, formData);
          } else {
            await createUser(formData);
          }
          closeUserForm();
        } catch (err) {
            setError(err.message);
        }
      };

      return { users, headers, handleDeleteUser, editUser, editingUser, showUserForm, openUserForm, closeUserForm, handleSubmit }
    }
  }
</script>

<template>
  <div class="table-container">
    <div class="create-new-button-container">
      <button class="btn btn-primary" @click="openUserForm">
        New user
      </button>
    </div>

    <EasyDataTable
      :headers="headers"
      :items="users"
      show-index
      border-cell
      alternating
    >
      <template #item-actions="item">
        <div class="action-buttons">
          <button class="btn btn-primary" @click="editUser(item)">Edit</button>
          <button class="btn btn-danger" @click="handleDeleteUser(item)">Delete</button>
        </div>
      </template>
    </EasyDataTable>

    <!-- Modal to create and edit new user -->
    <div v-if="showUserForm" class="modal-overlay">
      <div class="modal-content">
        <button class="close-btn" @click="closeUserForm">X</button>

        <UserForm
          :mustConfigure="false"
          :isRegister="false"
          :initialData="editingUser"
          :onSubmit="handleSubmit"
        />
      </div>
    </div>
  </div>
</template>
