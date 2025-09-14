<script>
  import { ref, computed } from 'vue';
  import { useRouter } from 'vue-router';
  import UsersManagement from '../UsersManagement.vue';
  import AppsManagement from '../AppsManagement.vue';
  import DepartmentsManagement from '../DepartmentsManagement.vue';
  import PositionsManagement from '../PositionsManagement.vue';
  import { useUserStore } from '../../store/user';


  export default {
    components: {
      UsersManagement,
      AppsManagement,
      DepartmentsManagement,
      PositionsManagement,
    },

    setup() {
      const router = useRouter();
      const userStore = useUserStore();

      const activeTab = ref('users-management');

      const userFullName = computed(() =>
        userStore.user ? `${userStore.user.firstname} ${userStore.user.lastname}` : ''
      );

      const logout = () => {
        localStorage.removeItem('token');
        userStore.clearUser();
        router.push('/login');
      };

      const changeTab = (tab) => {
        activeTab.value = tab;
      };

      return { logout, userFullName, activeTab, changeTab };
    },
  };
</script>

<template>
  <div class="container dashboard-page">
    <div class="inner-container">
      <div class="left-bar">
        <h1> Welcome to your dashboard</h1>
        <p>{{ userFullName }}</p>
        <a href="#" class="link" @click.prevent="logout"> Log out </a>
      </div>

      <div class="right-bar">
        <div class="dashboard-navigation">
          <div
            class="nav-item"
            :class="{ active: activeTab === 'users-management' }"
            @click="changeTab('users-management')"
          >
            Users
          </div>

          <div
            class="nav-item"
            :class="{ active: activeTab === 'apps-management' }"
            @click="changeTab('apps-management')"
          >
            Apps
          </div>

          <div
            class="nav-item"
            :class="{ active: activeTab === 'department-management' }"
            @click="changeTab('department-management')"
          >
            Departments
          </div>

          <div
            class="nav-item"
            :class="{ active: activeTab === 'positions-management' }"
            @click="changeTab('positions-management')"
          >
            Positions
          </div>
        </div>

        <div class="management-container">
          <div v-show="activeTab === 'users-management'" class="users-management">
            <UsersManagement />
          </div>

          <div v-show="activeTab === 'apps-management'" class="apps-management">
            <AppsManagement />
          </div>

          <div v-show="activeTab === 'department-management'" class="department-management">
            <DepartmentsManagement />
          </div>

          <div v-show="activeTab === 'positions-management'" class="positions-management">
            <PositionsManagement />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
  .dashboard-page {
    .inner-container {
      display: grid;
      grid-template-columns: 1fr 4fr;
      justify-content: space-between;
      width: 100%;
      height: 90vh;
      max-width: 1200px;
      background-color: $white;
      border-radius: $border-radius;
      box-shadow: $box-shadow;
      overflow: hidden;

      .left-bar {
        display: flex;
        flex-direction: column;
        background-color: $primary;
        color: $white;
        gap: 20px;
        padding: 20px;

        a {
          color: white;
        }
      }

      .right-bar {
        overflow-y: auto;
        max-height: 90vh;
        width: 100%;

        .dashboard-navigation {
          display: flex;
          justify-content: space-evenly;
          width: 100%;
          border-bottom: 1px solid #ddd;
          background-color: $white;

          .nav-item {
            padding: 20px 30px;
            cursor: pointer;
            transition: 0.4s ease-in;

            &.active {
              color: $primary;
              border-bottom: 2px solid $primary;
            }
          }
        }

        .management-container {
          padding: 20px;

          .table-container {
            .create-new-button-container {
              width: 300px;
            }

            .vue3-easy-data-table__main {
              .action-buttons {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
              }
            }
          }
        }
      }
    }
  }
</style>
