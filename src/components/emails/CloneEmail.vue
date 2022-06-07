<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("new-email-from-template-title") }}</h2>
      </header>

      <section>
        <form @submit.prevent="cloneEmail">
          <label for="title">{{ $t("title") }}</label>
          <input type="text" name="title" id="title" v-model="title" required />

          <label for="description">{{ $t("description") }}</label>
          <input
            type="text"
            name="description"
            id="description"
            v-model="description"
            required
          />

          <label for="subject">{{ $t("object") }}</label>
          <input
            type="text"
            name="subject"
            id="subject"
            v-model="subject"
            disabled
            readonly
          />

          <label for="content">{{ $t("content") }}</label>
          <textarea
            name="content"
            id="content"
            v-model="content"
            disabled
            readonly
          ></textarea>

          <div>
            <button type="submit" class="button">
              {{ $t("add") }}
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
  name: "CloneEmail",
  props: ["selected_email"],
  data() {
    return {
      title: "",
      description: "",
      subject: "",
      content: "",
    };
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);

    API.getEmail(this.selected_email["email_id"]).then((response) => {
      this.subject = response["subject"];
      this.content = response["content"];
    });
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    cloneEmail: function () {
      API.addTemplateEmail(
        this.selected_email["email_id"],
        this.title,
        this.description
      )
        .then(() => {
          this.$notify(
            {
              group: "success",
              title: this.$t("new-email-from-template"),
              text: this.$t("new-email-msg"),
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
              text: this.$t("new-email-from-template-error"),
            },
            3500
          );
        });
    },
  },
};
</script>
