class ConsultationSession {
  final int consultationId;
  final String channelName;
  final String rtcToken;
  final String sessionStatus;
  final int conversationId;
  final String? startedAt;
  final String role;

  const ConsultationSession({
    required this.consultationId,
    required this.channelName,
    required this.rtcToken,
    required this.sessionStatus,
    required this.conversationId,
    this.startedAt,
    required this.role,
  });

  factory ConsultationSession.fromJson(Map<String, dynamic> json) {
    return ConsultationSession(
      consultationId: json['consultation_id'] as int,
      channelName: json['channel_name'] as String,
      rtcToken: json['rtc_token'] as String,
      sessionStatus: json['session_status'] as String,
      conversationId: json['conversation_id'] as int,
      startedAt: json['started_at'] as String?,
      role: json['role'] as String,
    );
  }
}
