<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("send-email-users") }}</h2>
      </header>

      <section>
        <form @submit.prevent="sendEmails">
          <label for="email">{{ $t("email") }}</label>
          <select name="email" id="email" v-model="email_id" required>
            <option value="" selected>{{ $t("select-email") }}</option>
            <option
              v-for="email in emails"
              :key="email['email_id']"
              :value="email['email_id']"
            >
              {{ email["title"] }}
            </option>
          </select>
          <div>
            <button type="submit" class="button">
              {{ $t("send") }}
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
  name: "SendEmailUsers",
  props: ["selected_users"],
  data() {
    return {
      emails: {},
      email_id: "",
    };
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);

    API.getEmails("", "").then((response) => {
      this.emails = response;
    });
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    sendEmails: function () {
      API.sendEmails(
        this.email_id,
        Object.keys(this.selected_users).map((key) => this.selected_users[key])
      )
        .then(() => {
          this.$notify(
            {
              group: "success",
              title: this.$t("email-send-users"),
              text: this.$t("email-send-users-msg"),
            },
            3500
          );
          this.$emit("component", { name: "" });
        })
        .catch(() => {
          this.$notify(
            {
              group: "error",
              title: this.$t("error"),
              text: this.$t("email-send-user-error"),
            },
            3500
          );
          this.$emit("component", { name: "" });
        });
    },
  },
};
</script>
