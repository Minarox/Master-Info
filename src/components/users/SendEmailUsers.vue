<template>
  <div class="popup">
    <article>
      <header>
        <h2>Envoi d'email aux utilisateurs</h2>
      </header>

      <section>
        <form @submit.prevent="sendEmails">
          <label for="email">Email :</label>
          <select name="email" id="email" v-model="email_id" required>
            <option value="" selected>Sélectionnez un email</option>
            <option v-for="email in emails" :key="email['email_id']" :value="email['email_id']">{{ email["title"] }}</option>
          </select>
          <div>
            <button
                type="submit"
                class="button"
            >
              Envoyer
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
  name: "SendEmailUsers",
  props: ["selected_users"],
  data() {
    return {
      emails: {},
      email_id: ''
    }
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);

    API.getEmails('', '').then(response => {
      this.emails = response;
    });
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    sendEmails: function() {
      API.sendEmails(this.email_id, Object.keys(this.selected_users).map((key) => this.selected_users[key]))
        .then(() => {
          this.$notify({
            group: "success",
            title: "Email envoyé",
            text: "L'email à bien été envoyé aux utilisateurs."
          }, 3500);
          this.$emit('component', { name: '' });
        })
        .catch(() => {
          this.$notify({
            group: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors de l'envoie."
          }, 3500);
          this.$emit('component', { name: '' });
        });
    }
  }
};
</script>