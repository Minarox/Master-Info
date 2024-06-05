package chatProject.model.user;

import java.util.Objects;

/**
 * This class models the account of a user.
 * A user has only a username.
 * There is no need for an ID.
 */
public class UserAccount {

    /**
     * The name of a user.
     */
    private final String username;

    public UserAccount(String username) {
        this.username = username;
    }

    /**
     * Gets the username of a user.
     * @return the username
     */
    public String getUsername() {
        return username;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;
        UserAccount that = (UserAccount) o;
        return Objects.equals(username, that.username);
    }

    @Override
    public int hashCode() {
        return Objects.hash(username);
    }

    @Override
    public String toString() {
        return username;
    }

}
