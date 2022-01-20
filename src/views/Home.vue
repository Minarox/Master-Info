<template>
  <div>
    <Header :username="user['username']" :admin="user['is_admin']" />
    <transition name="fade">
      <component v-if="component" :is="component" :group="group" @component="switchComponent" />
    </transition>

    <main>
      <div>
        <article class="users">
          <header>
            <h1>Utilisateurs sans groupe :</h1>
            <i class="fas fa-sync-alt" @click="updateUsers"></i>
          </header>
          <section>
            <p v-for="user in usersWithoutGroup" :key="user['id']">{{ user["username"] }}</p>
          </section>
        </article>

        <article class="group" v-if="group">
          <header>
            <h2>Groupe actuel :</h2>
            <i class="fas fa-sync-alt" @click="updateGroup"></i>
          </header>
          <section>
            <div>
              <p>Nom du groupe :</p>
              <p>{{ group["group"]["name"] }}</p>
            </div>
            <div>
              <p>Code d'invitation :</p>
              <p>{{ group["group"]["link"] }}</p>
            </div>
            <div>
              <p>Nombre de membres :</p>
              <p>{{ group["users"].length }}</p>
            </div>
            <div>
              <p>Date de création :</p>
              <p>{{ group["group"]["created_at"] }}</p>
            </div>
          </section>

          <header>
            <h2>Membres :</h2>
          </header>
          <section>
            <p v-for="user in group['users']" :key="user['id']" :class="(user['id'] === group['group']['admin']) ? 'group_admin' : ''">
              {{ user["username"] }}
            </p>
          </section>
          <section>
            <button v-if="user['id'] === group['group']['admin']" type="button" class="button" @click="component = 'EditGroup'">Modifier le groupe</button>
            <button type="button" class="button btn-warning" @click="component = 'LeaveGroup'">Quitter le groupe</button>
          </section>
        </article>

        <article class="no_group" v-else>
          <header>
            <h2>Invitation et groupe :</h2>
          </header>
          <section>
            <div>
              <form @submit.prevent="formCode">
                <label for="code">Code :</label>
                <div>
                  <input type="text" name="code" id="code" v-model="code" maxlength="16" required>
                  <button type="submit">Valider</button>
                </div>
              </form>
            </div>
            <div>
              <button type="button" class="button" @click="component = 'AddGroup'">Créer un nouveau groupe</button>
              <button type="button" class="button btn-warning" @click="component = 'RandomGroup'">Rejoindre aléatoirement un groupe</button>
            </div>
          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import Header from "../components/global/Header";
import AddGroup from "../components/home/AddGroup";
import EditGroup from "../components/home/EditGroup";
import LeaveGroup from "../components/home/LeaveGroup";
import RandomGroup from "../components/home/RandomGroup";
import {API} from "../assets/js/api";

export default {
  name: "Home",
  components: {
    Header,
    AddGroup,
    EditGroup,
    LeaveGroup,
    RandomGroup
  },
  data() {
    return {
      component: '',
      code: '',
      users: [],
      user: JSON.parse(localStorage.getItem("session")),
      group: null,
      session_handler: null,
      users_handler: null,
      group_handler: null
    }
  },
  mounted() {
    if (this.isAdmin(false))
      this.$router.push('/admin');

    this.updateSession();
    this.session_handler = setInterval(() => {
      this.updateSession();
    }, 6000);

    this.updateUsers();
    this.users_handler = setInterval(() => {
      this.updateUsers();
    }, 6000);

    if (this.user["group_id"]) {
      this.updateGroup();
      this.group_handler = setInterval(() => {
        this.updateGroup();
      }, 6000);
    }
  },
  beforeUnmount() {
    clearInterval(this.session_handler);
    clearInterval(this.users_handler);
    clearInterval(this.group_handler);
  },
  methods: {
    updateSession() {
      API.currentSession().then(response => {
        console.log("refresh session");
        this.user = response;
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
    updateGroup() {
      API.getCurrentGroup().then(response => {
        console.log("refresh group");
        this.group = response;
      }).catch(() => {
        clearInterval(this.group_handler);
        this.group = null;
      })
    },
    formCode() {
      if (this.code.length < 16) {
        // Error message
      } else {
        API.joinGroup(this.code).then(() => {
          this.updateSession();
          this.updateUsers();
        }).catch(error => {
          this.unauthorizedError(error);
          // Error message
        })
      }
    }
  },
  computed: {
    usersWithoutGroup() {
      let array = [];
      for (let i = 0; i < this.users.length; i++)
        if (!this.users[i]["group_id"] && this.users[i]["id"] !== this.user["id"])
          array.push({username: this.users[i]["username"]});
      return array;
    },
    groupID() {
      return this.user["group_id"];
    }
  },
  watch: {
    groupID(value) {
      if (value) {
        this.updateGroup();
        this.group_handler = setInterval(() => {
          this.updateGroup();
        }, 8000);
      } else {
        clearInterval(this.group_handler);
        this.group = null;
      }
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
  transition: transform .2s ease-in-out;
}

article > header i:hover {
  transform: scale(1.2);
}

article > section {
  display: flex;
  flex-flow: row nowrap;
  gap: 20px;
}

.users section {
  flex-flow: row wrap;
}

.users p {
  flex: 1 0 calc(33% - 20px);
  user-select: all;
}

.group > section:first-of-type {
  margin-bottom: 26px;
}

.group > section:first-of-type > div {
  flex: 1 0 calc(25% - 20px);
  display: flex;
  flex-flow: column nowrap;
  gap: 4px;
}

.group > section:first-of-type > div > p:first-of-type {
  color: var(--text-label);
}

.group > section:first-of-type > div > p:last-of-type {
  text-align: center;
  user-select: all !important;
}

.group > section:nth-of-type(2) {
  margin-bottom: 26px;
}

.group > section:nth-of-type(2) > p {
  flex: 1 0 calc(33% - 20px);
  user-select: all;
}

.group_admin {
  text-decoration: underline;
}

.group > section:last-of-type {
  justify-content: center;
  gap: 20px 120px;
}

.group > section:last-of-type .button {
  margin: 0;
}

.no_group section {
  justify-content: space-between;
}

.no_group section > div:first-of-type {
  flex: 0 0 calc(70% - 20px);
  margin-top: 10px;
}

.no_group section > div:first-of-type form div {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  gap: 20px;
}

.no_group section > div:last-of-type {
  flex: 0 0 calc(30% - 20px);
  border-left: 1px solid var(--text-label);
  padding-left: 20px;
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  gap: 12px;
}

.no_group section > div:last-of-type .button {
  width: 100%;
}
</style>