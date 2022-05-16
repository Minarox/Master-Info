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
  deleteUsers,

  // Email
  getEmails,
  addEmail,
  sendEmails,
  getEmail,
  editEmail,
  addTemplateEmail,
  deleteEmail,

  // Log
  getLogs,

  // Statistic
  getGlobalStats,
  getAvgUsageMonth,
  getAvgUsageDay,
  getAvgUsageHour,
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
      { grant_type: "password", email: email.toString(), password: password.toString() },
      headers()
    )
    .then(response => {
      response["data"]["expires_at"] = Date.now() / 1000 + response["data"]["expires_in"];
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
          {email: email.toString(), first_name: first_name.toString(), last_name: last_name.toString()},
          headers()
      )
      .then(() => {
        this.userInfo().then(response => {
          return response["data"];
        })
      });
}

function editPassword(old_password, new_password, confirm_new_password) {
  return axios
      .put(
          api_url + "/userinfo/password",
          {old_password: old_password.toString(), new_password: new_password.toString(), confirm_new_password: confirm_new_password.toString()},
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

function getAdmins(email, first_name, last_name, scope, active) {
  return axios
      .get(
          api_url + "/admins?email=" + email.toString() + "&first_name=" + first_name.toString() + "&last_name=" + last_name.toString() + "&scope=" + scope.toString() + "&active=" + active.toString(),
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function addAdmin(email, password, confirm_password, first_name, last_name, scope) {
  return axios
      .post(
          api_url + "/admins",
          {email: email.toString(), password: password.toString(), confirm_password: confirm_password.toString(), first_name: first_name.toString(), last_name: last_name.toString(), scope: scope.toString()},
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
          {email: email.toString(), first_name: first_name.toString(), last_name: last_name.toString(), scope: scope.toString(), active: active.toString()},
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
          {new_password: new_password.toString(), confirm_new_password: confirm_new_password.toString()},
          headers()
      )
      .then(response => {
        return response["data"];
      });
}

function getUsers(email, first_name, last_name, device) {
  return axios
      .get(
          api_url + "/users?email=" + email.toString() + "&first_name=" + first_name.toString() + "&last_name=" + last_name.toString() + "&device=" + device.toString(),
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
          {email: email.toString(), first_name: first_name.toString(), last_name: last_name.toString(), device: device.toString()},
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
            {email: email.toString(), first_name: first_name.toString(), last_name: last_name.toString(), device: device.toString()},
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

function deleteUsers(users) {
    return axios
        .put(
            api_url + "/users/delete",
            {users: users},
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function getEmails(title, description) {
    return axios
        .get(
            api_url + "/emails?title=" + title.toString() + "&description=" + description.toString(),
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
            {title: title.toString(), description: description.toString(), subject: subject.toString(), content: content.toString()},
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
            {email_id: email_id.toString(), users: users},
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
            {title: title.toString(), description: description.toString(), subject: subject.toString(), content: content.toString()},
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
            {title: title.toString(), description: description.toString()},
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

function getLogs(source, source_type, action, target, target_type) {
        return axios
        .get(
            api_url + "/logs?source=" + source.toString() + "&source_type=" + source_type.toString() + "&action=" + action.toString() + "&target=" + target.toString() + "&target_type=" + target_type.toString(),
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function getGlobalStats() {
        return axios
        .get(
            api_url + "/statistics",
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function getAvgUsageMonth() {
        return axios
        .get(
            api_url + "/statistics/avg-usage-month",
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function getAvgUsageDay() {
        return axios
        .get(
            api_url + "/statistics/avg-usage-day",
            headers()
        )
        .then(response => {
            return response["data"];
        });
}

function getAvgUsageHour() {
        return axios
        .get(
            api_url + "/statistics/avg-usage-hour",
            headers()
        )
        .then(response => {
            return response["data"];
        });
}