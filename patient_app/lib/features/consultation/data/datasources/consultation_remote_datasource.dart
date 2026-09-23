import '../../../../core/network/api_client.dart';
import '../../domain/entities/chat_message.dart';
import '../../domain/entities/consultation_session.dart';

class ConsultationRemoteDataSource {
  final ApiClient apiClient;

  ConsultationRemoteDataSource({required this.apiClient});

  Future<ConsultationSession> joinConsultation(int consultationId) async {
    final response = await apiClient.post('/api/v1/consultations/$consultationId/join');
    final data = response.data['data'] as Map<String, dynamic>;
    return ConsultationSession.fromJson(data);
  }

  Future<void> leaveConsultation(int consultationId) async {
    await apiClient.post('/api/v1/consultations/$consultationId/leave');
  }

  Future<void> endConsultation(int consultationId, {String? reason}) async {
    await apiClient.post('/api/v1/consultations/$consultationId/end', data: {
      if (reason != null) 'reason': reason,
    });
  }

  Future<List<ChatMessage>> getMessages(int conversationId) async {
    final response = await apiClient.get('/api/v1/conversations/$conversationId/messages');
    final items = response.data['data'] as List<dynamic>;
    return items.map((e) => ChatMessage.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<ChatMessage> sendMessage(int conversationId, String content, String clientMsgId) async {
    final response = await apiClient.post('/api/v1/conversations/$conversationId/messages', data: {
      'content': content,
      'client_msg_id': clientMsgId,
      'message_type': 'text',
    });
    final data = response.data['data'] as Map<String, dynamic>;
    return ChatMessage.fromJson(data);
  }

  Future<void> sendHeartbeat(String status) async {
    await apiClient.post('/api/v1/presence/heartbeat', data: {'status': status});
  }
}
