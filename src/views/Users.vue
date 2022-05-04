<template>
  <div>
    <Header :current_user="current_user" @component="switchComponent" />
    <transition name="fade">
      <component
        v-if="component"
        :is="component"
        :current_user="current_user"
        :selected_user="selected_user"
        @component="switchComponent"
      />
    </transition>

    <main>
      <div>
        <article>
          <form>
            <div>
              <label for="email">Email</label>
              <input type="email" name="email" id="email">
            </div>
            <div>
              <label for="first_name">Prénom</label>
              <input type="text" name="first_name" id="first_name">
            </div>
            <div>
              <label for="last_name">Nom</label>
              <input type="text" name="last_name" id="last_name">
            </div>
            <div>
              <label for="device">Appareil</label>
              <input type="text" name="device" id="device">
            </div>
            <div>
              <button class="button" type="submit">Rechercher</button>
              <button class="button btn-back" type="reset">Réinitialiser</button>
            </div>
          </form>
        </article>

        <article>
          <table>
            <!-- Head -->
            <thead>
            <tr>
              <th>&nbsp;</th>
              <th>Email</th>
              <th>Nom complet</th>
              <th>Appareil</th>
              <th>Ajouté le</th>
              <th>Actions</th>
            </tr>
            </thead>
            <!-- Content -->
            <tbody v-if="users">
            <tr v-for="user in users" :key="user['user_id']">
              <td><input type="checkbox" name="name1" />&nbsp;</td>
              <td>{{ user["email"] }}</td>
              <td>{{ user["first_name"] + ' ' + user["last_name"] }}</td>
              <td>{{ user["device"] }}</td>
              <td>{{ user["created_at"].split('.')[0] }}</td>
              <td>
                <a href="" @click.prevent="selected_user = user; component = 'EditUser'">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M490.3 40.4C512.2 62.27 512.2 97.73 490.3 119.6L460.3 149.7L362.3 51.72L392.4 21.66C414.3-.2135 449.7-.2135 471.6 21.66L490.3 40.4zM172.4 241.7L339.7 74.34L437.7 172.3L270.3 339.6C264.2 345.8 256.7 350.4 248.4 353.2L159.6 382.8C150.1 385.6 141.5 383.4 135 376.1C128.6 370.5 126.4 361 129.2 352.4L158.8 263.6C161.6 255.3 166.2 247.8 172.4 241.7V241.7zM192 63.1C209.7 63.1 224 78.33 224 95.1C224 113.7 209.7 127.1 192 127.1H96C78.33 127.1 64 142.3 64 159.1V416C64 433.7 78.33 448 96 448H352C369.7 448 384 433.7 384 416V319.1C384 302.3 398.3 287.1 416 287.1C433.7 287.1 448 302.3 448 319.1V416C448 469 405 512 352 512H96C42.98 512 0 469 0 416V159.1C0 106.1 42.98 63.1 96 63.1H192z"/></svg>
                </a>
                <a href="" @click.prevent="selected_user = user; component = 'EditUser'">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M464 64C490.5 64 512 85.49 512 112C512 127.1 504.9 141.3 492.8 150.4L275.2 313.6C263.8 322.1 248.2 322.1 236.8 313.6L19.2 150.4C7.113 141.3 0 127.1 0 112C0 85.49 21.49 64 48 64H464zM217.6 339.2C240.4 356.3 271.6 356.3 294.4 339.2L512 176V384C512 419.3 483.3 448 448 448H64C28.65 448 0 419.3 0 384V176L217.6 339.2z"/></svg>
                </a>
                <a href="" @click.prevent="selected_user = user; component = 'DeleteUser'">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM394.8 466.1C393.2 492.3 372.3 512 346.9 512H101.1C75.75 512 54.77 492.3 53.19 466.1L31.1 128H416L394.8 466.1z"/></svg>
                </a>
              </td>
            </tr>
            </tbody>
            <tbody v-else>
            <tr>
              <td colspan="4">Rien n'a été trouvé.</td>
            </tr>
            </tbody>
          </table>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import EditUserInfo from "@/components/session/EditUserInfo";
import UserInfo from "@/components/session/UserInfo";
import EditPassword from "@/components/session/EditPassword";
import DeleteUser from "@/components/users/DeleteUser";
import Header from "@/components/global/Header";
import {API} from "@/assets/js/api";

export default {
  name: "Users",
  components: {
    Header,
    EditUserInfo,
    UserInfo,
    EditPassword,
    DeleteUser
  },
  data() {
    return {
      current_user: JSON.parse(localStorage.getItem("user")),
      component: "",
      users: [],
      selected_user: []
    };
  },
  mounted() {
    API.getUsers()
      .then(response => {
        this.users = response;
      })
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
  background-color: var(--bg-content);
  box-shadow: rgba(149, 157, 165, 0.2) 0 8px 24px 0;
}

article:last-of-type {
  overflow: auto;
  height: calc(100vh - 64px - 122px - 30px * 3);
}

form {
  padding: 20px;
  display: flex;
  flex-flow: row nowrap !important;
  gap: 20px;
}

form button {
  margin: 0;
}

form > * {
  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
  width: 100%;
  gap: 10px;
}

table {
  width: 100%;
  border-spacing: 0;
}

table thead tr {
  position: sticky;
  top: 0;
  background-color: var(--bg-content-header);
  box-shadow: 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
}

table thead th {
  padding: 10px;
  text-align: left;
}

table tbody td {
  padding: 8px;
}

table tbody td:last-of-type {
  display: flex;
  align-items: center;
  gap: 12px;
}

table tbody td:last-of-type > * {
  display: inline-block;
  object-fit: cover;
  width: 22px;
  height: 22px;
}

table tbody tr td[colspan='4'] {
  text-align: center;
  background-color: var(--bg-content);
  padding: calc((100vh / 2) - 64px - 30px * 2) 0;
}

table tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, .05);
}
</style>
