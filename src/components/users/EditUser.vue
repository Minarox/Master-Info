<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("edit-user-title") }}</h2>
      </header>

      <section>
        <form @submit.prevent="editUser">
          <label for="email">{{ $t("email") }}</label>
          <input type="email" name="email" id="email" v-model="email" required>

          <label for="first_name">{{ $t("first-name") }}</label>
          <input type="text" name="first_name" id="first_name" v-model="first_name" required>

          <label for="last_name">{{ $t("last-name") }}</label>
          <input type="text" name="last_name" id="last_name" v-model="last_name" required>

          <label for="device">{{ $t("device") }}</label>
          <input type="text" name="device" id="device" v-model="device">
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
  name: "EditUser",
  props: ["selected_user"],
  data() {
    return {
      email: this.selected_user["email"],
      first_name: this.selected_user["first_name"],
      last_name: this.selected_user["last_name"],
      device: this.selected_user["device"]
    }
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editUser: function() {
      API.editUser(this.selected_user["user_id"], this.email, this.first_name, this.last_name, this.device)
        .then(() => {
          this.$notify({
            group: "success",
            title: this.$t("edit-user"),
            text: this.$t("edit-user-msg")
          }, 3500);
          this.$emit('component', { name: '' });
          this.$emit('reload');
        })
        .catch(() => {
          this.$notify({
            group: "error",
            title: this.$t("error"),
            text: this.$t("edit-user-error")
          }, 3500);
        });
    }
  }
};
</script>