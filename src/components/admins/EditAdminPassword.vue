<template>
  <div class="popup">
    <article>
      <header>
        <h2>
          {{ $t("edit-admin-title") }}<br>
          {{ full_name }}
        </h2>
      </header>

      <section>
        <form @submit.prevent="editAdminPassword">
          <label for="password">{{ $t("new-password") }}</label>
          <input type="password" name="password" id="password" v-model="password" required>

          <label for="confirm_password">{{ $t("new-password-confirm") }}</label>
          <input type="password" name="v" id="confirm_password" v-model="confirm_password" required>

          <div>
            <button
                type="submit"
                class="button"
            >
              {{ $t("edit") }}
            </button>
            <button
              type="button"
              class="button btn-back"
              @click="$emit('component', { name: '' })"
            >
              {{ $t("close") }}
            </button>
          </div>
        </form>
      </section>
    </article>
  </div>
</template>

<script>
import {API} from "@/assets/js/api";

export default {
  name: "EditAdminPassword",
  props: ["selected_admin"],
  data() {
    return {
      password: '',
      confirm_password: ''
    }
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editAdminPassword: function() {
      API.editAdminPassword(this.selected_admin["admin_id"], this.password, this.confirm_password)
        .then(() => {
          this.$notify({
            group: "success",
            title: this.$t("edited-password"),
            text: this.$t("edited-password-msg")
          }, 3500);
          this.$emit('component', { name: '' });
          this.$emit('reload');
        })
        .catch(() => {
          this.$notify({
            group: "error",
            title: this.$t("error"),
            text: this.$t("edit-error")
          }, 3500);
        });
    }
  },
  computed: {
    full_name: function () {
      return this.selected_admin["first_name"] + ' ' + this.selected_admin["last_name"];
    }
  }
};
</script>
