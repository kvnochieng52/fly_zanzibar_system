<template>
  <Head title="Permissions Management" />

  <DashboardLayout>
    <div class="container">
      <h1>Permissions Management</h1>
      <p>Debug: {{ permissions }}</p>

      <div v-if="permissions && permissions.data">
        <p>Found {{ permissions.data.length }} permissions</p>
        <ul>
          <li v-for="permission in permissions.data" :key="permission.id">
            {{ permission.name }}
          </li>
        </ul>
      </div>

      <div v-else>
        <p>No permissions data found</p>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

export default {
  name: "PermissionIndexSimple",
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    permissions: Object,
  },

  mounted() {
    console.log("Permissions data:", this.permissions);
  },
};
</script>