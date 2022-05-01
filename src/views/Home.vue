<template>
  <div>
    <Header :username="user['username']" :admin="user['is_admin']" />
    <transition name="fade">
      <component
        v-if="component"
        :is="component"
        :group="group"
        @component="switchComponent"
      />
    </transition>

    <main>
      <div>
        <article class="users">
          <header>
            <h1>Utilisateurs sans groupe :</h1>
            <i class="fas fa-sync-alt" @click="updateUsers"></i>
          </header>
          <section>
            <p v-for="user in usersWithoutGroup" :key="user['id']">
              {{ user["username"] }}
            </p>
          </section>
        </article>

        <article class="group" v-if="group">
          <header>
            <h2>Groupe actuel :</h2>
            <i class="fas fa-sync-alt" @click="updateGroup"></i>
          </header>
          <section>
            <p>
              Nom du groupe : <span>{{ group["group"]["name"] }}</span>
            </p>
            <p>
              Code d'invitation : <span>{{ group["group"]["link"] }}</span>
            </p>
            <p>
              Nombre de membres : <span>{{ group["users"].length }}</span>
            </p>
            <p>
              Date de création : <span>{{ group["group"]["created_at"] }}</span>
            </p>
          </section>
          <hr />
          <header>
            <h2>Membres :</h2>
          </header>
          <section>
            <p
              v-for="user in group['users']"
              :key="user['id']"
              :class="
                user['id'] === group['group']['admin'] ? 'group_admin' : ''
              "
            >
              {{ user["username"] }}
            </p>
          </section>
          <section>
            <button
              v-if="user['id'] === group['group']['admin']"
              type="button"
              class="button"
              @click="component = 'EditGroup'"
            >
              Modifier le groupe
            </button>
            <button
              type="button"
              class="button btn-warning"
              @click="component = 'LeaveGroup'"
            >
              Quitter le groupe
            </button>
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
                  <input
                    type="text"
                    name="code"
                    id="code"
                    v-model="code"
                    maxlength="16"
                    required
                  />
                  <button type="submit">Valider</button>
                </div>
              </form>
            </div>
            <div>
              <button
                type="button"
                class="button"
                @click="component = 'AddGroup'"
              >
                Créer un nouveau groupe
              </button>
              <button
                type="button"
                class="button btn-warning"
                @click="component = 'RandomGroup'"
              >
                Rejoindre aléatoirement un groupe
              </button>
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
import { API } from "../assets/js/api";

export default {
  name: "Users",
  components: {
    Header,
    AddGroup,
    EditGroup,
    LeaveGroup,
    RandomGroup,
  },
  data() {
    return {
      component: "",
      code: "",
      users: [],
      user: JSON.parse(localStorage.getItem("session")),
      group: null,
      session_handler: null,
      users_handler: null,
      group_handler: null,
    };
  },
  mounted() {
    if (this.isAdmin(false)) this.$router.push("/admin");

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
      API.currentSession()
        .then((response) => {
          console.log("refresh session");
          this.user = response;
        })
        .catch((error) => {
          this.unauthorizedError(error);
        });
    },
    updateUsers() {
      API.getUsers()
        .then((response) => {
          console.log("refresh users");
          this.users = response;
        })
        .catch((error) => {
          this.unauthorizedError(error);
        });
    },
    updateGroup() {
      API.getCurrentGroup()
        .then((response) => {
          console.log("refresh group");
          this.group = response;
        })
        .catch(() => {
          clearInterval(this.group_handler);
          this.group = null;
        });
    },
    formCode() {
      if (this.code.length < 16) {
        // Error message
      } else {
        API.joinGroup(this.code)
          .then(() => {
            this.updateSession();
            this.updateUsers();
          })
          .catch((error) => {
            this.unauthorizedError(error);
            // Error message
          });
      }
    },
  },
  computed: {
    usersWithoutGroup() {
      let array = [];
      for (let i = 0; i < this.users.length; i++)
        if (
          !this.users[i]["group_id"] &&
          this.users[i]["id"] !== this.user["id"]
        )
          array.push({ username: this.users[i]["username"] });
      return array;
    },
    groupID() {
      return this.user["group_id"];
    },
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
    },
  },
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
  transition: transform 0.2s ease-in-out;
}

article > header i:hover {
  transform: scale(1.2);
}

article > section {
  padding: 0 12px;
  display: flex;
  flex-flow: row nowrap;
  gap: 8px 20px;
}

.users section {
  flex-flow: row wrap;
}

.users p {
  flex: 1 0 calc(33% - 20px * 2);
  user-select: all;
}

.group > hr {
  margin: 24px auto;
  width: 92%;
}

.group > section:first-of-type {
  flex-flow: row wrap;
  gap: 8px 12px;
}

.group > section:first-of-type > p {
  flex: 1 0 calc(50% - 12px * 2);
  color: var(--text-label);
}

.group > section:first-of-type > p span {
  color: var(--text);
  user-select: all !important;
}

.group > section:nth-of-type(2) {
  flex-flow: row wrap;
  margin-bottom: 26px;
}

.group > section:nth-of-type(2) > p {
  flex: 1 0 calc(33% - 20px * 2);
  user-select: all;
}

.group_admin {
  text-decoration: underline;
}

.group > section:last-of-type {
  justify-content: center;
  gap: 20px 60px;
}

.group > section:last-of-type .button {
  margin: 0;
}

.no_group section {
  flex-flow: row nowrap;
}

.no_group section > div:first-of-type {
  flex: 1 0 calc(58% - 20px * 2);
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
}

.no_group section > div:first-of-type form div {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  gap: 12px;
}

.no_group section > div:last-of-type {
  border-left: 1px solid var(--text-label);
  padding-left: 20px;
  flex: 1 0 calc(42% - 20px * 2);
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

.no_group section > div:last-of-type .button {
  margin: 0;
  width: 100%;
}

@media only screen and (max-width: 800px) {
  .group > section:first-of-type {
    flex-flow: column wrap;
  }

  .group > section:first-of-type > p {
    flex: 1 0 100%;
  }

  .no_group section {
    flex-flow: column nowrap;
  }

  .no_group section > div:last-of-type {
    margin-top: 20px;
    border-left: none;
    padding-left: 0;
    flex: 1 0 100%;
    flex-flow: row nowrap;
    align-items: center;
    justify-content: center;
    gap: 12px 40px;
  }

  .no_group section > div:last-of-type .button {
    width: auto;
  }
}

@media only screen and (max-width: 700px) {
  .users p {
    flex: 1 0 calc(50% - 20px * 2);
  }

  .group > section:first-of-type > p {
    flex: 1 0 calc(50% - 12px * 2);
  }
}

@media only screen and (max-width: 500px) {
  article > section {
    padding: 0;
  }

  .group > section:last-of-type {
    padding: 0;
    flex-flow: column nowrap;
    justify-content: center;
  }

  .no_group section > div:first-of-type form div {
    flex-flow: column nowrap;
  }

  .no_group section > div:last-of-type {
    margin-top: 24px;
    flex-flow: column nowrap;
    align-items: center;
    justify-content: center;
    gap: 12px;
  }

  .no_group section > div:last-of-type .button,
  .no_group section > div:first-of-type form div button {
    width: 100%;
  }
}

@media only screen and (max-width: 400px) {
  .users section {
    gap: 8px;
  }

  .users p {
    flex: 1 0 100%;
  }

  .group > section:nth-of-type(2) {
    flex-flow: row wrap;
    gap: 8px;
  }

  .group > section:nth-of-type(2) > p {
    flex: 1 0 100%;
    user-select: all;
  }
}
</style>
