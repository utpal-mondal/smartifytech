# AI Customer Service Chat Flow

## Overview

The chatbox on the frontend is backed by an AI service that uses OpenAI's function-calling API. It can answer customer questions and, when asked about stock or product availability, query the local `products` table before replying.

## Components

- **Chatbox UI** (`resources/views/layouts/app.blade.php`, `public/css/whatsapp.css`)
- **Chat API** (`routes/api.php`, `app/Http/Controllers/AiController.php`)
- **AI Service** (`app/Services/AiServices.php`)
- **AI Tool Service** (`app/Services/AiToolServices.php`)
- **Conversations / Messages tables** (`chat_conversations`, `chat_messages`)

## Interaction Flow

1. **Visitor opens chatbox**
   - User is asked for `name` and `email`.
   - After clicking **Start chat**, the pre-chat form is hidden and the conversation area is shown.

2. **Visitor sends a message**
   - Chatbox posts `POST /api/chat/message` with:
     - `message`
     - `name`
     - `email`
   - The route lives in `routes/api.php` and uses `web` middleware so session and CSRF protection still work.
   - The request includes the Laravel CSRF token.

3. **Controller handles the request** (`AiController::chat`)
   - Validates the payload.
   - Finds or creates a `ChatConversation` keyed by `session_id`.
   - Stores the user's message in `chat_messages` with `role = user`.
   - Calls `AiServices::reply($message)` to get an AI response.
   - Stores the agent's message in `chat_messages` with `role = agent`.
   - Updates `last_message_at` on the conversation.
   - Returns `{'message': '...'}` as JSON.

4. **AI Service calls OpenAI** (`AiServices::reply`)
   - Builds a chat request with:
     - A system prompt describing the agent's role.
     - A list of available tools.
   - Sends the request to OpenAI (`chat.completions`).

5. **OpenAI may call a tool**
   - `get_product_stock` – for questions about stock or quantity.
   - `search_products` – for questions about whether a product is available.
   - The model supplies a `product` or `product_name` argument.

6. **Tool Service looks up the product** (`AiToolServices`)
   - Searches the `products` table in this order:
     1. Numeric ID match (`Product::find`).
     2. Exact `model` match.
     3. Any `model` contained in the user's text.
   - Returns a string such as:
     - "Product SM-1000 is in stock, quantity: 500"
     - "Product SM-1000 is available, quantity: 500"
     - "product out of stock"
     - "Product not found or not available."

7. **AI Service sends the tool result back to OpenAI**
   - The final reply is generated from the tool result.

8. **Final response is returned and displayed**
   - `AiController` sends the message to the chatbox.
   - The chatbox appends it to the conversation.

## How to Configure

Add the OpenAI key to `.env`:

```env
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini
```

Then clear the config cache:

```bash
php artisan config:clear
```

## Demo Product Names

The `ProductSeeder` creates 50 demo products, for example:

- `SM-1001`
- `SM-1025`
- `SM-1050`

You can ask the chatbox:

- "Is SM-1000 in stock?"
- "Quantity of SM-2000?"
- "Is SM-3000 available?"
