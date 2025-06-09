<?php require_once 'vues/includes/header.php'; ?>

<div class="chatgpt-bg">
  <div class="chatgpt-container">
    <div class="chatgpt-header">
      <i class="fas fa-robot"></i> Assistant de Gestion de Stock
      <div class="header-actions">
        <button id="download-chat" class="btn btn-sm btn-outline-light" title="Télécharger l'historique">
          <i class="fas fa-download"></i>
        </button>
        <button id="clear-chat" class="btn btn-sm btn-outline-light" title="Effacer l'historique">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
    <div class="chatgpt-messages" id="chat-messages">
      <div class="message bot">
        <div class="bubble bot">
          <button class="btn btn-sm btn-outline-secondary copy-btn" title="Copier la réponse">
            <i class="fas fa-copy"></i>
          </button>
          <p>Bonjour ! Je suis votre assistant intelligent de gestion de stock, propulsé par l'IA. Je peux vous aider avec :</p>
          <ul>
            <li>Les informations sur le stock des produits</li>
            <li>Les détails des catégories</li>
            <li>Les informations sur les fournisseurs</li>
            <li>Des recommandations pour optimiser votre stock</li>
            <li>Des analyses de tendances</li>
          </ul>
          <p>Comment puis-je vous aider aujourd'hui ?</p>
        </div>
      </div>
    </div>
    <form id="chat-form" class="chatgpt-input-row" onsubmit="return false;">
      <div class="input-group">
        <input type="text" id="question" name="question" class="form-control chatgpt-input" placeholder="Posez votre question..." required>
        <button type="button" id="send-button" class="btn btn-primary chatgpt-send">
          <i class="fas fa-paper-plane"></i>
          <div class="spinner-border spinner-border-sm d-none" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </button>
      </div>
    </form>
  </div>
</div>

<style>
body, html {
  height: 100%;
  margin: 0;
  padding: 0;
}
.chatgpt-bg {
  min-height: 100vh;
  background: #f6f7f9;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 40px 0;
}
.chatgpt-container {
  width: 100%;
  max-width: 900px;
  background: transparent;
  display: flex;
  flex-direction: column;
  min-height: 80vh;
}
.chatgpt-header {
  background: #1677ff;
  color: #fff;
  font-size: 1.3rem;
  font-weight: 600;
  padding: 22px 32px 18px 32px;
  border-radius: 18px 18px 0 0;
  margin-bottom: 0;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 2px 8px rgba(22,119,255,0.08);
}
.chatgpt-messages {
  flex: 1;
  padding: 32px 0 16px 0;
  display: flex;
  flex-direction: column;
  gap: 18px;
  overflow-y: auto;
  min-height: 300px;
  max-height: 60vh;
}
.message {
  display: flex;
  width: 100%;
  position: relative;
}
.message.user { justify-content: flex-end; }
.message.bot { justify-content: flex-start; }
.bubble {
  padding: 1.1rem 1.5rem;
  border-radius: 18px;
  font-size: 1rem;
  max-width: 70%;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  word-break: break-word;
  line-height: 1.6;
  position: relative;
}
.bubble.user {
  background: #1677ff;
  color: #fff;
  border-bottom-right-radius: 6px;
  margin-left: auto;
  margin-right: 0;
}
.bubble.bot {
  background: #fff;
  color: #222;
  border-bottom-left-radius: 6px;
  margin-left: 0;
  margin-right: auto;
}
.copy-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  opacity: 0;
  transition: opacity 0.2s;
  background: transparent;
  border: none;
  color: #6c757d;
  padding: 4px;
}
.copy-btn:hover {
  color: #1677ff;
  background: rgba(0,0,0,0.05);
  border-radius: 4px;
}
.bubble:hover .copy-btn {
  opacity: 1;
}
.chatgpt-input-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 18px 32px 24px 32px;
  background: transparent;
  border-radius: 0 0 18px 18px;
}
.chatgpt-input {
  flex: 1;
  border-radius: 16px;
  border: 1px solid #e0e0e0;
  padding: 0.9rem 1.2rem;
  font-size: 1rem;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.chatgpt-send {
  border-radius: 16px;
  padding: 0.7rem 1.2rem;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  gap: 5px;
}
.header-actions {
  margin-left: auto;
  display: flex;
  gap: 10px;
}
.spinner-border {
  margin-left: 5px;
}
@media (max-width: 900px) {
  .chatgpt-container { max-width: 100vw; }
  .chatgpt-header, .chatgpt-input-row { padding-left: 10px; padding-right: 10px; }
  .bubble { max-width: 90vw; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const chatForm = document.getElementById('chat-form');
  const questionInput = document.getElementById('question');
  const chatMessages = document.getElementById('chat-messages');
  const sendButton = document.getElementById('send-button');

  function ajouterMessageUser(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message user';
    messageDiv.innerHTML = `<div class="bubble user">${message}</div>`;
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function ajouterMessageBot(message, source) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message bot';
    messageDiv.innerHTML = `
      <div class="bubble bot">
        <button class="btn btn-sm btn-outline-secondary copy-btn" title="Copier la réponse">
          <i class="fas fa-copy"></i>
        </button>
        ${message}
      </div>`;
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Ajouter l'événement de copie
    const copyBtn = messageDiv.querySelector('.copy-btn');
    copyBtn.addEventListener('click', function() {
      navigator.clipboard.writeText(message).then(() => {
        const originalTitle = this.title;
        this.title = 'Copié !';
        setTimeout(() => {
          this.title = originalTitle;
        }, 2000);
      });
    });
  }

  function ajouterMessageErreur(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'message bot';
    errorDiv.innerHTML = `<div class="bubble bot" style="background:#f8d7da;color:#721c24;">${message}</div>`;
    chatMessages.appendChild(errorDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  // Gestionnaire pour effacer l'historique
  document.getElementById('clear-chat').addEventListener('click', function() {
    if (confirm('Voulez-vous vraiment effacer tout l'historique de la conversation ?')) {
      const firstMessage = chatMessages.querySelector('.message.bot');
      chatMessages.innerHTML = '';
      if (firstMessage) {
        chatMessages.appendChild(firstMessage);
      }
    }
  });

  // Fonction pour télécharger l'historique
  document.getElementById('download-chat').addEventListener('click', function() {
    const messages = Array.from(chatMessages.querySelectorAll('.message')).map(msg => {
      const isUser = msg.classList.contains('user');
      const content = msg.querySelector('.bubble').textContent.trim();
      return `${isUser ? 'Vous' : 'Assistant'}: ${content}`;
    });

    const chatHistory = messages.join('\n\n');
    const blob = new Blob([chatHistory], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `historique-chat-${new Date().toISOString().slice(0,10)}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  });

  // Amélioration de la gestion des erreurs
  async function envoyerQuestion(question, retryCount = 0) {
    const maxRetries = 3;
    const retryDelay = 1000; // 1 seconde

    try {
      const response = await fetch('api.php?question=' + encodeURIComponent(question));
      if (!response.ok) {
        const error = await response.text();
        throw new Error(error);
      }
      const data = await response.json();
      if (data.erreur) {
        throw new Error(data.erreur);
      }
      return data;
    } catch (error) {
      if (retryCount < maxRetries) {
        console.log(`Tentative ${retryCount + 1} échouée, nouvelle tentative dans ${retryDelay}ms...`);
        await new Promise(resolve => setTimeout(resolve, retryDelay));
        return envoyerQuestion(question, retryCount + 1);
      }
      throw error;
    }
  }

  // Remplacer l'événement submit du formulaire par un clic sur le bouton
  sendButton.addEventListener('click', async function() {
    const question = questionInput.value.trim();
    if (!question) return;
    
    ajouterMessageUser(question);
    questionInput.value = '';
    
    const spinner = this.querySelector('.spinner-border');
    
    this.disabled = true;
    spinner.classList.remove('d-none');
    
    try {
      const data = await envoyerQuestion(question);
      ajouterMessageBot(data.reponse, data.source);
    } catch (error) {
      console.error('Erreur:', error);
      let errorMessage = 'Désolé, une erreur est survenue.';
      
      if (error.message.includes('Failed to fetch')) {
        errorMessage = 'Impossible de se connecter au serveur. Veuillez vérifier votre connexion internet.';
      } else if (error.message.includes('timeout')) {
        errorMessage = 'La requête a expiré. Veuillez réessayer.';
      } else {
        errorMessage = `Erreur: ${error.message}`;
      }
      
      ajouterMessageErreur(errorMessage);
    } finally {
      this.disabled = false;
      spinner.classList.add('d-none');
    }
  });

  // Ajouter la possibilité d'envoyer avec la touche Entrée
  questionInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      sendButton.click();
    }
  });
});
</script>

<?php require_once 'vues/includes/footer.php'; ?>