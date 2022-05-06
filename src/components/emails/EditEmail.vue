<template>
  <div class="popup">
    <article>
      <header>
        <h2>Modification de l'email</h2>
      </header>

      <section>
        <form @submit.prevent="editEmail">
          <label for="title">Titre :</label>
          <input type="text" name="title" id="title" v-model="title" required>

          <label for="description">Description :</label>
          <input type="text" name="description" id="description" v-model="description" required>

          <label for="subject">Objet :</label>
          <input type="text" name="subject" id="subject" v-model="subject" required>

          <label for="content">Contenu :</label>
          <textarea name="content" id="content" v-model="content" required></textarea>

          <div>
            <button
                type="submit"
                class="button"
            >
              Modifier
            </button>
            <button
              type="button"
              class="button btn-back"
              @click="$emit('component', { name: '' })"
            >
              Fermer
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
  name: "EditEmail",
  props: ["selected_email"],
  data() {
    return {
      title: this.selected_email["title"],
      description: this.selected_email["description"],
      subject: '',
      content: ''
    }
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
    API.getEmail(this.selected_email["email_id"]).then(response => {
      this.subject = response["subject"];
      this.content = response["content"];
    })
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editEmail: function() {
      API.editEmail(this.selected_email["email_id"], this.title, this.description, this.subject, this.content)
        .then(() => {
          this.$emit('component', { name: '' });
          this.$emit('reload');
        });
    }
  }
};
</script>
