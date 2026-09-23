class ChatMessage {
  final int id;
  final int conversationId;
  final int senderId;
  final String? clientMsgId;
  final String messageType;
  final String? content;
  final bool isRead;
  final String? createdAt;
  final String? senderName;

  const ChatMessage({
    required this.id,
    required this.conversationId,
    required this.senderId,
    this.clientMsgId,
    required this.messageType,
    this.content,
    required this.isRead,
    this.createdAt,
    this.senderName,
  });

  factory ChatMessage.fromJson(Map<String, dynamic> json) {
    final senderObj = json['sender'] as Map<String, dynamic>?;
    return ChatMessage(
      id: json['id'] as int,
      conversationId: json['conversation_id'] as int,
      senderId: json['sender_id'] as int,
      clientMsgId: json['client_msg_id'] as String?,
      messageType: json['message_type'] as String? ?? 'text',
      content: json['content'] as String?,
      isRead: json['is_read'] as bool? ?? false,
      createdAt: json['created_at'] as String?,
      senderName: senderObj != null ? senderObj['full_name'] as String? : null,
    );
  }
}
