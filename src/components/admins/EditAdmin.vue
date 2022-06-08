<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("edit-admin-title") }}</h2>
      </header>

      <section>
        <form @submit.prevent="editAdmin">
          <label for="email">{{ $t("email") }}</label>
          <input
            type="email"
            name="email"
            id="email"
            v-model="email"
            required
          />

          <label for="first_name">{{ $t("first-name") }}</label>
          <input
            type="text"
            name="first_name"
            id="first_name"
            v-model="first_name"
            required
          />

          <label for="last_name">{{ $t("last-name") }}</label>
          <input
            type="text"
            name="last_name"
            id="last_name"
            v-model="last_name"
            required
          />

          <label for="scope">{{ $t("type") }}</label>
          <select name="scope" id="scope" v-model="scope" required>
            <option value=""></option>
            <option value="app">App</option>
            <option value="admin">Admin</option>
            <option value="super_admin">Super admin</option>
          </select>

          <label for="active">{{ $t("active") }}</label>
          <select name="active" id="active" v-model="active" required>
            <option value=""></option>
            <option value="1">✅</option>
            <option value="0">❌</option>
          </select>

          <div>
            <button type="submit" class="button">
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
import { API } from "../../assets/js/api";

export default {
  name: "EditAdmin",
  props: ["selected_admin", "current_user"],
  data() {
    return {
      email: this.selected_admin["email"],
      first_name: this.selected_admin["first_name"],
      last_name: this.selected_admin["last_name"],
      scope: this.selected_admin["scope"],
      active: this.selected_admin["active"],
    };
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editAdmin: function () {
      API.editAdmin(
        this.selected_admin["admin_id"],
        this.email,
        this.first_name,
        this.last_name,
        this.scope,
        this.active
      )
        .then(() => {
          this.$notify(
            {
              group: "success",
              title: this.$t("edit-admin"),
              text: this.$t("success-edit"),
            },
            3500
          );
          if (this.current_user["email"] === this.selected_admin["email"]) {
            API.userInfo().then(() => {
              this.$router.go(0);
            });
          } else {
            this.$emit("component", { name: "" });
            this.$emit("reload");
          }
        })
        .catch(() => {
          this.$notify(
            {
              group: "error",
              title: this.$t("error"),
              text: this.$t("edit-error"),
            },
            3500
          );
        });
    },
  },
};
</script>
