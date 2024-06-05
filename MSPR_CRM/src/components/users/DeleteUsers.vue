<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("users-delete-title") }}</h2>
      </header>

      <section>
        <form @submit.prevent="deleteUsers">
          <p>{{ $t("users-delete-confirm") }}</p>
          <div>
            <button type="submit" class="button btn-warning">
              {{ $t("remove") }}
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
  name: "DeleteUsers",
  props: ["selected_users"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteUsers: function () {
      API.deleteUsers(
        Object.keys(this.selected_users).map((key) => this.selected_users[key])
      )
        .then(() => {
          this.$notify(
            {
              group: "success",
              title: this.$t("users-delete"),
              text: this.$t("users-delete-msg"),
            },
            3500
          );
          this.$emit("component", { name: "" });
          this.$emit("reload");
        })
        .catch(() => {
          this.$notify(
            {
              group: "error",
              title: this.$t("error"),
              text: this.$t("user-delete-error"),
            },
            3500
          );
          this.$emit("component", { name: "" });
        });
    },
  },
};
</script>

<style scoped>
form p {
  text-align: center;
}
</style>
