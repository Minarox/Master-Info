<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("delete-email-title") }}</h2>
      </header>

      <section>
        <form @submit.prevent="deleteEmail">
          <p>
            {{ $t("delete-email-confirm") }} {{ selected_email["title"] }} ?
          </p>
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
  name: "DeleteEmail",
  props: ["selected_email"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteEmail: function () {
      API.deleteEmail(this.selected_email["email_id"])
        .then(() => {
          this.$notify(
            {
              group: "success",
              title: this.$t("delete-email"),
              text: this.$t("delete-email-msg"),
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
};
</script>

<style scoped>
form p {
  text-align: center;
}
</style>
