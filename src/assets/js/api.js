"use strict";
const axios = require("axios").default;
const api_url = "https://mspr.minarox.fr/api";

export const API = {
  // Session
  login,
  userInfo,
  editUserInfo,
  editPassword,
  logout,

  // Admin
  getAdmins,
  addAdmin,
  getAdmin,
  editAdmin,
  deleteAdmin,
  editAdminPassword,

  // User
  getUsers,
  addUser,
  getUser,
  editUser,
  deleteUser,

  // Email
  getEmails,
  addEmail,
  sendEmails,
  getEmail,
  editEmail,
  addTemplateEmail,
  deleteEmail,

  // Log
  getLogs
};

function headers() {
  let session = JSON.parse(localStorage.getItem("session"));

  if (session && session["access_token"]) {
    return {
      headers: {
        Authorization: "Bearer " + session["access_token"],
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

function login(email, password) {
  return axios
    .post(
      api_url + "/login",
      { grant_type: "password", email: email, password: password },
      headers()
    )
    .then(response => {
      response["data"]["expires_at"] = response["data"]["expires_in"] + Date.now();
      localStorage.setItem("session", JSON.stringify(response["data"]));
      return response["data"];
    });
}

function userInfo() {
  return axios
      .get(
          api_url + "/userinfo",
          headers()
      )
      .then(response => {
        localStorage.setItem("user", JSON.stringify(response["data"]));
        return response["data"];
      });
}

function editUserInfo(email, first_name, last_name) {
  return axios
      .put(
          api_url + "/userinfo",
          {email: email, first_name: first_name, last_name: last_name},
          headers()
      )
      .then(response => {
        this.userInfo().then(() => {
          return response["data"];
        })
      });
}

function editPassword(old_password, new_password, confirm_new_password) {
  return axios
      .put(
          api_url + "/userinfo/password",
          {old_password: old_password, new_password: new_password, confirm_new_password: confirm_new_password},
          headers()
      )
      .then(response => {
        this.logout().then(() => {
          return response["data"];
        })
      });
}

function logout() {
  return axios
    .get(api_url + "/logout", headers())
    .then(response => {
      localStorage.removeItem("session");
      localStorage.removeItem("user");
      return response["data"];
    })
    .catch(error => {
      localStorage.removeItem("session");
      localStorage.removeItem("user");
      return error;
    });
}

function getAdmins() {
  return axios
      .get(
          api_url + "/admins",
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function addAdmin(email, password, confirm_password, first_name, last_name) {
  return axios
      .post(
          api_url + "/admins",
          {email: email, password: password, confirm_password: confirm_password, first_name: first_name, last_name: last_name},
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function getAdmin(admin_id) {
  return axios
      .get(
          api_url + "/admins/" + admin_id.toString(),
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function editAdmin(admin_id, email, first_name, last_name, scope, active) {
  return axios
      .put(
          api_url + "/admins/" + admin_id.toString(),
          {email: email, first_name: first_name, last_name: last_name, scope: scope, active: active},
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function deleteAdmin(admin_id) {
  return axios
      .delete(
          api_url + "/admins/" + admin_id.toString(),
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function editAdminPassword(admin_id, new_password, confirm_new_password) {
  return axios
      .put(
          api_url + "/admins/" + admin_id.toString() + "/password",
          {new_password: new_password, confirm_new_password: confirm_new_password},
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function getUsers() {
  return axios
      .get(
          api_url + "/users",
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function addUser(email, first_name, last_name, device) {
  return axios
      .post(
          api_url + "/users",
          {email: email, first_name: first_name, last_name: last_name, device: device},
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function getUser(user_id) {
    return axios
        .get(
            api_url + "/users/" + user_id.toString(),
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function editUser(user_id, email, first_name, last_name, device) {
    return axios
        .put(
            api_url + "/users/" + user_id.toString(),
            {email: email, first_name: first_name, last_name: last_name, device: device},
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function deleteUser(user_id) {
    return axios
        .delete(
            api_url + "/users/" + user_id.toString(),
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function getEmails() {
    return axios
        .get(
            api_url + "/emails",
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function addEmail(title, description, subject, content) {
    return axios
        .post(
            api_url + "/emails",
            {title: title, description: description, subject: subject, content: content},
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function sendEmails(email_id, users) {
    return axios
        .post(
            api_url + "/emails/send",
            {email_id: email_id, users: users},
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function getEmail(email_id) {
    return axios
        .get(
            api_url + "/emails/" + email_id.toString(),
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function editEmail(email_id, title, description, subject, content) {
    return axios
        .put(
            api_url + "/emails/" + email_id.toString(),
            {title: title, description: description, subject: subject, content: content},
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function addTemplateEmail(email_id, title, description) {
    return axios
        .post(
            api_url + "/emails/" + email_id.toString(),
            {title: title, description: description},
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function deleteEmail(email_id) {
        return axios
        .delete(
            api_url + "/emails/" + email_id.toString(),
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function getLogs(source, source_id, source_type, action, target, target_id, target_type) {
        return axios
        .get(
            api_url + "/logs?source=" + source.toString() + "&source_id=" + source_id.toString() + "&source_type=" + source_type.toString() + "&action=" + action.toString() + "&target=" + target.toString() + "&target_id=" + target_id.toString() + "&target_type=" + target_type.toString(),
            headers()
        )
        .then(response => {
            return response["data"];
        });
}