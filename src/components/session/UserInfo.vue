<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("your-profile") }}</h2>
      </header>

      <section>
        <form @submit.prevent="editUserInfo">
          <label for="email">{{ $t("email-address") }}</label>
          <input type="email" name="email" id="email" v-model="email" required />
          <label for="first_name">{{ $t("first-name") }}</label>
          <input type="text" name="first_name" id="first_name" v-model="first_name" required />
          <label for="last_name">{{ $t("last-name") }}</label>
          <input type="text" name="last_name" id="last_name" v-model="last_name" required />
          <div>
            <button
              type="submit"
              class="button"
            >
              {{ $t("edit") }}
            </button>
            <button
                type="button"
                class="button btn-warning"
                @click="$emit('component', { name: 'EditPassword' })"
            >
              {{ $t("edit-password-button") }}
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
  name: "UserInfo",
  props: ["current_user"],
  data() {
    return {
      email: this.current_user["email"],
      first_name: this.current_user["first_name"],
      last_name: this.current_user["last_name"]
    };
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editUserInfo() {
      API.editUserInfo(this.email, this.first_name, this.last_name)
          .then(() => {
            this.$notify({
              group: "success",
              title: this.$t("profile-edited-title"),
              text: this.$t("profile-edited")
            }, 3500);
            this.$router.go(0);
          })
          .catch(() => {
            this.$notify({
              group: "error",
              title: this.$t("error"),
              text: this.$t("edit-error")
            }, 3500);
        });
    },
  },
};
</script>