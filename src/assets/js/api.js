"use strict";
const axios = require("axios").default;
const api_url = "https://ic.minarox.fr/api";

export const API = {
    login,
    // currentSession,
    logout,
    //
    // getConfig,
    // setMaxUsers,
    // setUsersPerGroup,
    // setLastGroupConfig,
    // deleteUser,
    // getGroups,
    // deleteGroup,
    //
    // getCurrentGroup,
    // addGroup,
    getUsers,
    // leaveCurrentGroup,
    // joinRandomGroup,
    // joinGroup
};

function headers() {
    let session = JSON.parse(localStorage.getItem("session"));

    if (session && session["token"]) {
        return {
            headers: {
                'Authorization': session["username"] + ' ' + session["token"],
                'Content-Type': 'application/json'
            }
        };
    } else {
        return {
            headers: {
                'Content-Type': 'application/json'
            }
        };
    }
}

function login(username) {
    const data = {username: username};

    return axios.post(api_url + "/login", data, headers())
        .then(response => {
            localStorage.setItem("session", JSON.stringify(response.data));
            return response.data;
        });
}

function logout() {
    return axios.get(api_url + "/logout", headers())
        .then(response => {
            localStorage.removeItem("session");
            return response.data;
        })
        .catch(error => {
            localStorage.removeItem("session");
            return error;
        });
}

function getUsers() {
    return axios.get(api_url + "/group/users", headers())
        .then(response => {
            return response.data;
        });
}
