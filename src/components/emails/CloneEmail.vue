<template>
  <div class="popup">
    <article>
      <header>
        <h2>Nouvel email à partir d'un modèle</h2>
      </header>

      <section>
        <form @submit.prevent="cloneEmail">
          <label for="title">Titre :</label>
          <input type="text" name="title" id="title" v-model="title" required>

          <label for="description">Description :</label>
          <input type="text" name="description" id="description" v-model="description" required>

          <label for="subject">Objet :</label>
          <input type="text" name="subject" id="subject" v-model="subject" disabled readonly>

          <label for="content">Contenu :</label>
          <textarea name="content" id="content" v-model="content" disabled readonly></textarea>

          <div>
            <button
                type="submit"
                class="button"
            >
              Ajouter
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
  name: "CloneEmail",
  props: ["selected_email"],
  data() {
    return {
      title: '',
      description: '',
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
    cloneEmail: function() {
      API.addTemplateEmail(this.selected_email["email_id"], this.title, this.description)
        .then(() => {
          this.$emit('component', { name: '' });
          this.$emit('reload');
        });
    }
  }
};
</script>
