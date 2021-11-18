package chatProject.model.messages;

import org.junit.Test;

import static org.junit.Assert.*;

public class MessageTest {

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

}