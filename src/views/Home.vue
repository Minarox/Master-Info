<template>
  <div>
    <Header />

    <main>
      <div>
        <article>
          <header>
            <h1>Utilisateurs sans groupe :</h1>
            <i class="fas fa-sync-alt" @click="updateUsers"></i>
          </header>
          <section id="list_users">
            <p v-for="user in usersWithoutGroup" :key="user['id']">{{ user["username"] }}</p>
          </section>
        </article>

        <article>
          <header>
            <h2>Invitation et groupe :</h2>
          </header>
          <section>
            <div>
              <form>
                <label for="code">Code :</label>
                <div>
                  <input type="text" name="code" id="code" required>
                  <button type="submit">Valider</button>
                </div>
              </form>
            </div>
            <div>
              <a href="#">Créer un nouveau groupe</a>
              <a href="#">Rejoindre aléatoirement un groupe</a>
            </div>
          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import Header from "../components/Header";
import {API} from "../assets/js/api";

export default {
  name: "Home",
  components: {
    Header
  },
  data() {
    return {
      code: '',
      users: []
    }
  },
  mounted() {
    this.updateUsers();
    if (this.isAdmin(false)) {
      console.log("is admin");
    }
  },
  methods: {
    updateUsers() {
      API.getUsers().then(response => {
        this.users = response;
      });
    }
  },
  computed: {
    usersWithoutGroup() {
      let array = [];
      for (let i = 0; i < this.users.length; i++) {
        if (!this.users[i]["group_id"]) {
          array.push({
            username: this.users[i]["username"]
          })
        }
      }
      return array;
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

article > section {
  display: flex;
  flex-flow: row nowrap;
  gap: 20px;
}

#list_users {
  flex-flow: row wrap;
}

article > section > div:first-of-type {
  flex: 1 0 70%;
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
}

article > section > div:last-of-type {
  border-left: 1px solid var(--text-label);
  padding-left: 20px;
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

article > section > div:last-of-type a {
  width: 100%;
}

form label {
  display: block;
  margin: 0 0 6px 4px;
  color: var(--text-label);
}

form div {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  gap: 20px;
}

form input {
  display: block;
  width: 100%;
  outline: none;
  padding: 6px;
  border: none;
  border-bottom: 1px solid var(--text-label);
}

form button, article > section div:last-of-type a {
  display: block;
  margin: 0 auto;
  outline: none;
  cursor: pointer;
  border-radius: 3px;
  font-size: 14px;
  font-weight: 500;
  line-height: 16px;
  padding: 10px 24px;
  border: none;
  color: #fff;
  background-color: var(--btn);
  transition: background-color .17s ease,color .17s ease;
  text-decoration: none;
  text-align: center;
}

form button:hover {
  background-color: var(--btn-hover);
}
</style>