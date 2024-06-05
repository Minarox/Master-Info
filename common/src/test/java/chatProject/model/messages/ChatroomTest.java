package chatProject.model.messages;

import chatProject.model.user.Status;
import chatProject.model.user.UserAccount;
import chatProject.model.user.UserInfo;
import org.junit.Test;

import static org.junit.Assert.assertEquals;

public class ChatroomTest {

    @Test
    public void getId() {
        final Message<Object> message = new Message<>(null, null);

        assertEquals("The message ID is not the one set in the constructor",
                0, message.getId());
    }

    @Test
    public void getContent() {
        String content = "TEST";
        final Message<String> message = new Message<>(null, content);

        assertEquals("The message content is not the one set in the constructor",
                content, message.getMessage());
    }

    @Test
    public void getSender() {
        final UserInfo user = new UserInfo(new UserAccount("Sender"), Status.ACTIVE);
        final Message<String> message = new Message<>(user, null);

        assertEquals("The owner is not the one set in the constructor",
                user.toString(), message.getSender().toString());
    }

}