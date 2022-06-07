<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("user-delete-title") }}</h2>
      </header>

      <section>
        <form @submit.prevent="deleteUser">
          <p>{{ $t("user-delete-confirm") }} {{ full_name }} ?</p>
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
import { API } from "@/assets/js/api";

export default {
  name: "DeleteUser",
  props: ["selected_user"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteUser: function () {
      API.deleteUser(this.selected_user["user_id"])
        .then(() => {
          this.$notify(
            {
              group: "success",
              title: this.$("user-delete"),
              text: this.$("user-delete-msg"),
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
              text: this.$("user-delete-error"),
            },
            3500
          );
          this.$emit("component", { name: "" });
        });
    },
  },
  computed: {
    full_name: function () {
      return (
        this.selected_user["first_name"] + " " + this.selected_user["last_name"]
      );
    },
  },
};
</script>

<style scoped>
form p {
  text-align: center;
}
</style>
