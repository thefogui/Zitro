import { defineStore } from 'pinia';
import { needConfiguration } from '../services/adminService';

export const useConfigStore = defineStore('config', {
  state: () => ({
    mustConfigure: null,
  }),
  actions: {
    async fetchMustConfigure() {
      if (this.mustConfigure === null || this.mustConfigure === true) {
        this.mustConfigure = await needConfiguration();
      }
      return this.mustConfigure;
    },
    setMustConfigure(value) {
      this.mustConfigure = value;
    }
  },
  persist: true,
});
