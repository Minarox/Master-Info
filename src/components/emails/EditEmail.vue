<template>
  <div class="popup">
    <article>
      <header>
        <h2>{{ $t("edit-email-title") }}</h2>
      </header>

      <section>
        <form @submit.prevent="editEmail">
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
            required
          />

          <label for="content">{{ $t("content") }}</label>
          <textarea
            name="content"
            id="content"
            v-model="content"
            required
          ></textarea>

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
import { API } from "@/assets/js/api";

export default {
  name: "EditEmail",
  props: ["selected_email"],
  data() {
    return {
      title: this.selected_email["title"],
      description: this.selected_email["description"],
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
    editEmail: function () {
      API.editEmail(
        this.selected_email["email_id"],
        this.title,
        this.description,
        this.subject,
        this.content
      )
        .then(() => {
          this.$notify(
            {
              group: "success",
              title: this.$t("edit-email"),
              text: this.$t("success-edit"),
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
              text: this.$t("edit-error"),
            },
            3500
          );
        });
    },
  },
};
</script>
