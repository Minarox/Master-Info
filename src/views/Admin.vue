<template>
  <div>
    <Header :username="user['username']" :admin="user['is_admin']" />
    <transition name="fade">
      <component v-if="component" :is="component" :users="users" :groups="groups" :range="range" @component="switchComponent" />
    </transition>

    <main>
      <div>
        <article class="config" v-if="config">
          <header>
            <h1>Configuration de l'application :</h1>
            <i class="fas fa-sync-alt" @click="updateConfig"></i>
          </header>
          <section>
            <form @submit.prevent="configForm">
              <div>
                <label for="max_users">Nombre d'utilisateurs maximal :</label>
                <input type="number" name="max_users" id="max_users" v-model="maxUsers" required>
              </div>

              <div>
                <label for="users_per_group">Nombre d'utilisateurs par groupe :</label>
                <input type="number" name="user_per_group" id="users_per_group" v-model="usersPerGroup" required>
              </div>

              <div>
                <label for="last_group_mode">Configuration du dernier groupe :</label>
                <select name="last_group_mode" id="last_group_mode" v-model="lastGroupMode">
                  <option value="LAST_MIN">LAST_MIN</option>
                  <option value="LAST_MAX">LAST_MAX</option>
                </select>
              </div>

              <div>
                <button type="submit">Valider</button>
              </div>
            </form>
          </section>
        </article>

        <article class="groups" v-if="groups">
          <header>
            <h2>Groupes :</h2>
            <i class="fas fa-sync-alt" @click="updateGroups"></i>
          </header>
          <section>
            <div v-for="i in groups.length" :key="i">
              <header>
                <h3>{{ groups[i-1]["name"] }}</h3>
                <div>
                  <p>Nombre de membres : {{ groups[i-1]["users"].length }}</p>
                  <p>Code d'accès : {{ groups[i-1]["link"] }}</p>
                  <button type="button" class="button btn-warning" @click="range = i-1; component = 'DeleteGroup'">Supprimer</button>
                </div>
              </header>
              <section>
                <p v-for="user in groups[i-1]['users']" :key="user['id']">
                  {{ user["username"] }} {{ (user["id"] === groups[i-1]["admin"]) ? '(Administrateur)' : ''}}
                </p>
              </section>
            </div>
          </section>
        </article>

        <article class="users" v-if="users">
          <header>
            <h2>Utilisateurs :</h2>
            <i class="fas fa-sync-alt" @click="updateUsers"></i>
          </header>
          <section>
            <table>
              <thead>
                <tr>
                  <td>Nom</td>
                  <td>Connecté</td>
                  <td>Dans un groupe</td>
                  <td>Date de création</td>
                  <td>Actions</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="i in users.length" :key="i">
                  <td>{{ users[i-1]["username"] }}</td>
                  <td>
                    <span v-if="users[i-1]['expire']">
                      <span v-if="Date.parse(users[i-1]['expire']) > Date.now()">Oui</span>
                      <span v-else>Non</span>
                    </span>
                    <span v-else>Non</span>
                  </td>
                  <td>{{ (users[i-1]["group_id"]) ? 'Oui' : 'Non' }}</td>
                  <td>{{ users[i-1]["created_at"] }}</td>
                  <td>
                    <!-- <button type="button" class="button">Modifier</button> -->
                    <button type="button" class="button btn-warning" @click="range = i-1; component = 'DeleteUser'">Supprimer</button>
                  </td>
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
import Header from "../components/global/Header";
import DeleteGroup from "../components/admin/DeleteGroup";
import DeleteUser from "../components/admin/DeleteUser";
import {API} from "../assets/js/api";

export default {
  name: "Admin",
  components: {
    Header,
    DeleteGroup,
    DeleteUser
  },
  data() {
    return {
      component: '',
      range: '',
      config: null,
      maxUsers: '',
      usersPerGroup: '',
      lastGroupMode: '',
      groups: null,
      users: null,
      user: JSON.parse(localStorage.getItem("session")),
      config_handler: null,
      groups_handler: null,
      users_handler: null
    }
  },
  mounted() {
    if (this.isAdmin()) {
      this.updateConfig();
      this.config_handler = setInterval(() => {
        this.updateConfig();
      }, 6000);

      this.updateGroups();
      this.groups_handler = setInterval(() => {
        this.updateGroups();
      }, 6000);

      this.updateUsers();
      this.users_handler = setInterval(() => {
        this.updateUsers();
      }, 6000);
    }
  },
  unmounted() {
    clearInterval(this.config_handler);
    clearInterval(this.groups_handler);
    clearInterval(this.users_handler);
  },
  methods: {
    updateConfig() {
      API.getConfig().then(response => {
        console.log("refresh config");
        this.config = response;
        this.maxUsers = response["maxUsers"];
        this.usersPerGroup = response["usersPerGroup"];
        this.lastGroupMode = response["lastGroupMode"];
      }).catch(error => {
        this.unauthorizedError(error);
      });
    },
    updateGroups() {
      API.getGroups().then(response => {
        console.log("refresh groups");
        this.groups = response;
      }).catch(error => {
        this.unauthorizedError(error);
      });
    },
    updateUsers() {
      API.getUsers().then(response => {
        console.log("refresh users");
        this.users = response;
      }).catch(error => {
        this.unauthorizedError(error);
      });
    },
    configForm() {
      API.setMaxUsers(this.maxUsers).then(() => {
        API.setUsersPerGroup(this.usersPerGroup).then(() => {
          API.setLastGroupConfig(this.lastGroupMode).then(() => {
            // Success
            this.updateConfig();
          }).catch(() => {
            // Error
            this.updateConfig();
          });
        }).catch(() => {
          // Error
          this.updateConfig();
        });
      }).catch(() => {
        // Error
        this.updateConfig();
      });
    }
  }
};
</script>

<style scoped>
main > div {
  display: flex;
  flex-flow: column nowrap;
  gap: 30px;
}

article {
  border-radius: 6px;
  background-color: var(--popup-bg);
  box-shadow: rgba(149, 157, 165, 0.2) 0 8px 24px 0;
  padding: 20px;
}

article > header {
  font-size: 1.1em;
  margin-bottom: 12px;
  font-weight: 500;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
}

article > header i {
  cursor: pointer;
}

.config section form {
  display: flex;
  flex-flow: row wrap;
  gap: 20px;
}

.config section form > div {
  flex: 1 0 calc(33% - 20px);
  display: flex;
  flex-flow: column nowrap;
}

.config section form > div:last-of-type {
  align-items: center;
}

.groups > section {
  display: flex;
  flex-flow: column nowrap;
  gap: 20px;
}

.groups section > div {
  border-radius: 4px;
  box-shadow: rgb(0 0 0 / 12%) 0 8px 16px 0;
}

.groups section div > header {
  padding: 4px 4px 4px 12px;
  background-color: var(--btn);
  border-radius: 4px;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
}

.groups section div > header * {
  color: white;
}

.groups section div > header div {
  display: flex;
  flex-flow: row wrap;
  align-items: center;
  gap: 20px;
}

.groups section div > section {
  padding: 20px;
  display: flex;
  flex-flow: row wrap;
  align-items: center;
  gap: 20px;
}

.groups section div > section p {
  flex: 1 0 calc(25% - 20px);
}

.users table {
  width: 100%;
  border-spacing: 0;
}

.users table thead {
  box-shadow: rgb(0 0 0 / 12%) 0 8px 16px 0;
  border-radius: 4px;
}

.users table thead tr td {
  background-color: var(--btn);
  color: white;
  padding: 12px;
}

.users table thead tr td:first-of-type {
 border-radius: 4px 0 0 4px;
}

.users table thead tr td:last-of-type {
 border-radius: 0 4px 4px 0;
}

.users table tbody tr:nth-of-type(even) {
  background-color: rgba(0, 0, 0, .06);
}

.users table tr td {
  width: calc(100% / 5);
  padding: 8px 12px;
}

.users table tbody tr td:last-of-type {
  width: 100%;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: flex-end;
  gap: 24px;
}

.users table tbody tr td:last-of-type .button {
  margin: 0;
}

</style>