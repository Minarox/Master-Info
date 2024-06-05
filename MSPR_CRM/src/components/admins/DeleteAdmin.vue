<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("remove-admin") }}</h2>
      </header>

      <section>
        <form @submit.prevent="deleteAdmin">
          <p>{{ $t("remove-admin-msg") }} {{ full_name }} ?</p>
          <div>
            <button type="submit" class="button btn-warning">
              {{ $t("remove") }}
            </button>
            <button
              type="button"
              class="button btn-back"
              @click="$emit('component', { name: '' })"
            >
              {{ $t("cancel") }}
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
  name: "DeleteAdmin",
  props: ["selected_admin"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteAdmin: function () {
      API.deleteAdmin(this.selected_admin["admin_id"])
        .then(() => {
          this.$notify(
            {
              group: "success",
              title: this.$t("delete-admin"),
              text: this.$t("delete-admin-msg"),
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
              text: this.$t("delete-error"),
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
        this.selected_admin["first_name"] +
        " " +
        this.selected_admin["last_name"]
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
