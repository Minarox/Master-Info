const OpenAI = require("openai-api");
const readline = require("readline");

const openai = new OpenAI(process.env.OPENAI_API_KEY);
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
});
let prompt = null;

function question() {
    rl.question("Vous: ", async (input) => {
        prompt += `Demande: ${input}\nRéponse:`;
        const gptResponse = await openai.complete({
            engine: "text-davinci-003",
            prompt: prompt,
            maxTokens: 500,
            temperature: 0.9,
            topP: 1,
            presencePenalty: 0,
            frequencyPenalty: 0,
            bestOf: 1,
            n: 1,
            stream: false,
            stop: ['\n', "testing"]
        });

        const response = gptResponse.data.choices[0].text.trim();
        prompt += `${response}\n`;
        console.log(`Réponse: ${response}`);
        rl.prompt();
        question();
    });
}

rl.question("Personnalisation du chatbot: ", async (input) => {
    prompt = `${input}\n\n`;
    question();
});
