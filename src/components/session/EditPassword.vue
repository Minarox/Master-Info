<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("edit-password-title") }}</h2>
      </header>

      <section>
        <form @submit.prevent="editPassword">
          <label for="old_password">{{ $t("old-password") }}</label>
          <input type="email" name="old_password" id="old_password" v-model="old_password" required />
          <label for="new_password">{{ $t("new-password") }}</label>
          <input type="text" name="new_password" id="new_password" v-model="new_password" required />
          <label for="confirm_new_password">{{ $t("new-password-confirm") }}</label>
          <input type="text" name="confirm_new_password" id="confirm_new_password" v-model="confirm_new_password" required />
          <div>
            <button type="submit" class="button">
              {{ $t("confirm") }}
            </button>
            <button
                type="button"
                class="button btn-back"
                @click="$emit('component', { name: 'UserInfo' })"
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
  name: "EditPassword",
  data() {
    return {
      old_password: '',
      new_password: '',
      confirm_new_password: ''
    };
  },
  mounted() {
    this.addEvents("UserInfo", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("UserInfo", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editPassword() {
      API.editPassword(this.old_password, this.new_password, this.confirm_new_password)
          .then(() => {
            this.$notify({
              group: "success",
              title: this.$t("password-edited"),
              text: this.$t("password-edited-msg")
            }, 3500);
            this.$router.push("/logout");
          })
          .catch(() => {
            this.$notify({
              group: "error",
              title: this.$t("error"),
              text: this.$t("password-edited-msg")
            }, 3500);
          });
    },
  },
};
</script>