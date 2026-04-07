import axios from 'axios';

export const evolutionApi = (url: string, apiKey: string) => {
  const instance = axios.create({
    baseURL: url,
    headers: {
      'apikey': apiKey,
      'Content-Type': 'application/json',
    },
  });

  return {
    async getInstances() {
      const { data } = await instance.get('/instance/fetchInstances');
      return data;
    },
    async getInstanceStatus(instanceName: string) {
      const { data } = await instance.get(`/instance/connectionState/${instanceName}`);
      return data;
    },
    async getContacts(instanceName: string) {
      const { data } = await instance.get(`/contact/getContacts/${instanceName}`);
      return data;
    },
    async sendMessage(instanceName: string, number: string, text: string) {
      // Formatação automática para Brasil - prefixo 55 e dígito 9
      let num = number.replace(/\D/g, '');
      if (num.length >= 10 && !num.startsWith('55')) {
        num = '55' + num;
      }

      const { data } = await instance.post(`/message/sendText/${instanceName}`, {
        number: num,
        text,
      });
      return data;
    },
    async connectInstance(instanceName: string) {
      const { data } = await instance.get(`/instance/connect/${instanceName}`);
      return data;
    },
    async createInstance(instanceName: string, token: string) {
      const { data } = await instance.post('/instance/create', {
        instanceName,
        token,
        qrcode: true,
      });
      return data;
    },
    async logoutInstance(instanceName: string) {
      const { data } = await instance.delete(`/instance/logout/${instanceName}`);
      return data;
    },
    async deleteInstance(instanceName: string) {
      const { data } = await instance.delete(`/instance/delete/${instanceName}`);
      return data;
    },

    // ✅ MÉTODO CORRETO: POST /chat/findChats/{instance} com body vazio = lista todos os chats
    async findChats(instanceName: string) {
      const { data } = await instance.post(`/chat/findChats/${instanceName}`, {});
      // A API retorna um array direto de objetos com campos: id, remoteJid, pushName, unreadCount, lastMessage, profilePicUrl
      return Array.isArray(data) ? data : [];
    },

    // ✅ MÉTODO CORRETO: POST /chat/findMessages/{instance} com where.key.remoteJid
    async findMessages(instanceName: string, remoteJid: string, limit = 50) {
      const { data } = await instance.post(`/chat/findMessages/${instanceName}`, {
        where: {
          key: { remoteJid }
        },
        limit,
      });
      // A API retorna: { messages: { total, records: [...], pages, currentPage } }
      return data?.messages?.records ?? [];
    },

    async sendMedia(instanceName: string, number: string, mediaUrl: string, caption?: string) {
      const { data } = await instance.post(`/message/sendMedia/${instanceName}`, {
        number,
        media: mediaUrl,
        mediaType: 'image',
        caption: caption || '',
      });
      return data;
    },
  };
};
