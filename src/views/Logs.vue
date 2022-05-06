<template>
  <div>
    <Header :current_user="current_user" @component="switchComponent" />
    <transition name="fade">
      <component
        v-if="component"
        :is="component"
        :current_user="current_user"
        @component="switchComponent"
      />
    </transition>

    <main>
      <div>
        <article id="filter">
          <form @submit.prevent="getLogs">
            <div>
              <label for="source_type">Type de source</label>
              <select name="source_type" id="source_type" v-model="source_type">
                <option value=""></option>
                <option value="Admin">Administrateur</option>
                <option value="User">Utilisateur</option>
                <option value="Email">Email</option>
                <option value="App">Application</option>
              </select>
            </div>
            <div>
              <label for="source">Source</label>
              <input type="text" name="source" id="source" v-model="source">
            </div>
            <div>
              <label for="action">Action</label>
              <select name="action" id="action" v-model="action">
                <option value=""></option>
                <option value="Add">Ajout</option>
                <option value="Edit">Edition</option>
                <option value="EditPassword">Edition MDP</option>
                <option value="Remove">Suppression</option>
                <option value="EmailSend">Email envoyé</option>
              </select>
            </div>
            <div>
              <label for="target_type">Type de cible</label>
              <select name="target_type" id="target_type" v-model="target_type">
                <option value=""></option>
                <option value="Admin">Administrateur</option>
                <option value="User">Utilisateur</option>
                <option value="Email">Email</option>
                <option value="App">Application</option>
              </select>
            </div>
            <div>
              <label for="target">Cible</label>
              <input type="text" name="target" id="target" v-model="target">
            </div>
            <button class="svg-button" type="submit">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M500.3 443.7l-119.7-119.7c27.22-40.41 40.65-90.9 33.46-144.7C401.8 87.79 326.8 13.32 235.2 1.723C99.01-15.51-15.51 99.01 1.724 235.2c11.6 91.64 86.08 166.7 177.6 178.9c53.8 7.189 104.3-6.236 144.7-33.46l119.7 119.7c15.62 15.62 40.95 15.62 56.57 0C515.9 484.7 515.9 459.3 500.3 443.7zM79.1 208c0-70.58 57.42-128 128-128s128 57.42 128 128c0 70.58-57.42 128-128 128S79.1 278.6 79.1 208z"/></svg>
            </button>
            <button class="svg-button" type="reset" @click="resetForm">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM394.8 466.1C393.2 492.3 372.3 512 346.9 512H101.1C75.75 512 54.77 492.3 53.19 466.1L31.1 128H416L394.8 466.1z"/></svg>
            </button>
          </form>
        </article>

        <article id="main">
          <header>
            <h2>Journaux d'activités</h2>
            <div>
              <a href="" @click.prevent="getLogs" class="svg-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M496 48V192c0 17.69-14.31 32-32 32H320c-17.69 0-32-14.31-32-32s14.31-32 32-32h63.39c-29.97-39.7-77.25-63.78-127.6-63.78C167.7 96.22 96 167.9 96 256s71.69 159.8 159.8 159.8c34.88 0 68.03-11.03 95.88-31.94c14.22-10.53 34.22-7.75 44.81 6.375c10.59 14.16 7.75 34.22-6.375 44.81c-39.03 29.28-85.36 44.86-134.2 44.86C132.5 479.9 32 379.4 32 256s100.5-223.9 223.9-223.9c69.15 0 134 32.47 176.1 86.12V48c0-17.69 14.31-32 32-32S496 30.31 496 48z"/></svg>
              </a>
            </div>
          </header>
          <section>
            <table>
              <!-- Head -->
              <thead>
              <tr>
                <th>Type de source</th>
                <th>Source</th>
                <th>Action</th>
                <th>Type de cible</th>
                <th>Cible</th>
                <th>Ajouté le</th>
              </tr>
              </thead>
              <!-- Content -->
              <tbody v-if="logs">
              <tr v-for="log in logs" :key="log['log_id']">
                <td>{{ log["source_type"] }}</td>
                <td>{{ log["source"] }}</td>
                <td>{{ log["action"] }}</td>
                <td>{{ log["target_type"] }}</td>
                <td>{{ log["target"] }}</td>
                <td>{{ log["created_at"] }}</td>
              </tr>
              </tbody>
              <tbody v-else>
              <tr>
                <td colspan="6" class="empty-table">Aucune donnée n'a été trouvée.</td>
              </tr>
              </tbody>
            </table>
          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import Header from "@/components/global/Header";
import UserInfo from "@/components/session/UserInfo";
import EditPassword from "@/components/session/EditPassword";
import {API} from "@/assets/js/api";

export default {
  name: "Logs",
  components: {
    Header,
    UserInfo,
    EditPassword
  },
  data() {
    return {
      current_user: JSON.parse(localStorage.getItem("user")),
      component: "",
      logs: [],
      source_type: '',
      source: '',
      action: '',
      target_type: '',
      target: ''
    };
  },
  mounted() {
    this.getLogs();
  },
  methods: {
    getLogs() {
      API.getLogs(this.source, this.source_type, this.action, this.target, this.target_type)
          .then(response => {
            this.logs = response;
          })
          .catch(() => {
            this.logs = null;
          })
    },
    resetForm() {
      this.source_type = this.source = this.action = this.target_type = this.target = '';
      this.getLogs();
    }
  }
};
</script>

<style scoped>
#main > section {
  height: calc(100vh - 299px);
}
</style>
