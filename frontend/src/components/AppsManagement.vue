<script>
  import { ref, onMounted } from 'vue';
  import EasyDataTable from 'vue3-easy-data-table';
  import 'vue3-easy-data-table/dist/style.css';
  import { getApps, deleteApp, createApp, getApp, updateApp } from '../services/appService';
  import AppForm from './forms/AppForm.vue';

  export default {
    name: 'AppsManagement',

    components: {
      EasyDataTable,
      AppForm
    },

    setup(props) {
      const apps = ref([]);
      const headers = [
        { text: 'ID', value: 'id' },
        { text: 'Name', value: 'name' },
        { text: 'Url', value: 'url' },
        { text: 'Actions', value: 'actions' },
      ];
      const showAppForm = ref(false);
      const editingApp = ref(null);

      const fetchApps = async () => {
        try {
          apps.value = await getApps();
        } catch (err) {
          console.error('Error fetching apps:', err);
        }
      };

      const handleDeleteApp = async (app) => {
        await deleteApp(app.id);
        fetchApps();
      };

      const editApp = async (app) => {
        try {
          app = await getApp(app.id);
          editingApp.value = { ...app };
          opeAppForm(app);
        } catch (err) {
          console.error('Error fetching user:', err);
        }
        
      };

      onMounted(fetchApps);

      const opeAppForm = (app = null) => {
        editingApp.value = app;
        showAppForm.value = true;
      };

      const closeAppForm = () => {
        showAppForm.value = false;
        fetchApps();
      };

      const handleSubmit = async (formData, setError) => {
        try {
          if (editingApp.value.id) {
            await updateApp(editingApp.value.id, formData);
          } else {
            await createApp(formData);
          }
          closeAppForm();
        } catch (err) {
          setError(err.message);
        }
      };

      return { apps, headers, showAppForm, handleDeleteApp, editApp, editingApp, opeAppForm, closeAppForm, handleSubmit };
    },
  };
</script>

<template>
  <div class="table-container">
    <div class="create-new-button-container">
      <button class="btn btn-primary" @click="opeAppForm">
        New app
      </button>
    </div>

    <EasyDataTable
      :headers="headers"
      :items="apps"
      show-index
      border-cell
      alternating
    >
      <template #item-actions="item">
        <div class="action-buttons">
          <button class="btn btn-primary" @click="editApp(item)">Edit</button>
          <button class="btn btn-danger" @click="handleDeleteApp(item)">Delete</button>
        </div>
      </template>
    </EasyDataTable>

    <!-- Modal to create and edit new app -->
    <div v-if="showAppForm" class="modal-overlay">
      <div class="modal-content">
        <button class="close-btn" @click="closeAppForm">X</button>

        <AppForm
          :isCreate="!editingApp"
          :initialData="editingApp || {}"
          :onSubmit="handleSubmit"
        />
      </div>
    </div>
  </div>
</template>
