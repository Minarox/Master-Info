<template>
  <div>
    <Header :user="user" @component="switchComponent" />
    <transition name="fade">
      <component
        v-if="component"
        :is="component"
        :user="user"
        @component="switchComponent"
      />
    </transition>

    <main>
      <div>
        <article class="users">
          <header>
            <h1>Utilisateurs sans groupe :</h1>
            <i class="fas fa-sync-alt" @click="component = 'UserInfo'"></i>
          </header>
          <section>

          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import EditUserInfo from "@/components/session/EditUserInfo";
import UserInfo from "@/components/session/UserInfo";
import EditPassword from "@/components/session/EditPassword";
import Header from "@/components/global/Header";

export default {
  name: "Users",
  components: {
    Header,
    EditUserInfo,
    UserInfo,
    EditPassword
  },
  data() {
    return {
      user: JSON.parse(localStorage.getItem("user")),
      component: ""
    };
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
