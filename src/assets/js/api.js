"use strict";
const axios = require("axios").default;
const api_url = "https://mspr.minarox.fr/api";

export const API = {
  login,
  currentSession,
  logout,

  getConfig,
  setMaxUsers,
  setUsersPerGroup,
  setLastGroupConfig,
  deleteUser,
  getGroups,
  deleteGroup,

  getCurrentGroup,
  addGroup,
  editGroup,
  getUsers,
  leaveCurrentGroup,
  joinRandomGroup,
  joinGroup,
};

function headers() {
  let session = JSON.parse(localStorage.getItem("session"));

  if (session && session["token"]) {
    return {
      headers: {
        Authorization: session["username"] + " " + session["token"],
        "Content-Type": "application/json",
      },
    };
  } else {
    return {
      headers: {
        "Content-Type": "application/json",
      },
    };
  }
}

function login(username, password) {
  return axios
    .post(
      api_url + "/login",
      { username: username, password: password },
      headers()
    )
    .then((response) => {
      localStorage.setItem("session", JSON.stringify(response["data"]));
      return response["data"];
    });
}

function currentSession() {
  return axios.get(api_url + "/session", headers()).then((response) => {
    localStorage.setItem("session", JSON.stringify(response["data"]));
    return response["data"];
  });
}

function logout() {
  return axios
    .get(api_url + "/logout", headers())
    .then((response) => {
      localStorage.removeItem("session");
      return response["data"];
    })
    .catch((error) => {
      localStorage.removeItem("session");
      return error;
    });
}

function getConfig() {
  return axios.get(api_url + "/admin/config", headers()).then((response) => {
    return response["data"];
  });
}

function setMaxUsers(max_users) {
  return axios
    .post(api_url + "/admin/max-users", { max_users: max_users }, headers())
    .then((response) => {
      return response["data"];
    });
}

function setUsersPerGroup(users_per_group) {
  return axios
    .post(
      api_url + "/admin/users-per-group",
      { users_per_group: users_per_group },
      headers()
    )
    .then((response) => {
      return response["data"];
    });
}

function setLastGroupConfig(last_group_mode) {
  return axios
    .post(
      api_url + "/admin/last-group",
      { last_group_mode: last_group_mode },
      headers()
    )
    .then((response) => {
      return response["data"];
    });
}

function deleteUser(user_id) {
  return axios
    .delete(api_url + "/admin/user/" + user_id, headers())
    .then((response) => {
      return response["data"];
    });
}

function getGroups() {
  return axios.get(api_url + "/admin/groups", headers()).then((response) => {
    return response["data"];
  });
}

function deleteGroup(group_id) {
  return axios
    .delete(api_url + "/admin/group/" + group_id, headers())
    .then((response) => {
      return response["data"];
    });
}

function getCurrentGroup() {
  return axios.get(api_url + "/group", headers()).then((response) => {
    return response["data"];
  });
}

function addGroup(name) {
  return axios
    .post(api_url + "/group", { name: name }, headers())
    .then((response) => {
      return response["data"];
    });
}

function editGroup(name, admin) {
  return axios
    .put(api_url + "/group", { name: name, admin: admin }, headers())
    .then((response) => {
      return response["data"];
    });
}

function getUsers() {
  return axios.get(api_url + "/group/users", headers()).then((response) => {
    return response["data"];
  });
}

function leaveCurrentGroup() {
  return axios.get(api_url + "/group/leave", headers()).then((response) => {
    return response["data"];
  });
}

function joinRandomGroup() {
  return axios.get(api_url + "/group/random", headers()).then((response) => {
    return response["data"];
  });
}

function joinGroup(group_link) {
  return axios
    .get(api_url + "/group/join/" + group_link, headers())
    .then((response) => {
      return response["data"];
    });
}
