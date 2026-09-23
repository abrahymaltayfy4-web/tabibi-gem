import '../../domain/entities/chat_message.dart';
import '../../domain/entities/consultation_session.dart';

abstract class ConsultationState {
  const ConsultationState();
}

class ConsultationInitial extends ConsultationState {}

class ConsultationLoading extends ConsultationState {}

class ConsultationActive extends ConsultationState {
  final ConsultationSession session;
  final List<ChatMessage> messages;
  final bool isCameraOn;
  final bool isMuted;

  const ConsultationActive({
    required this.session,
    required this.messages,
    this.isCameraOn = true,
    this.isMuted = false,
  });

  ConsultationActive copyWith({
    ConsultationSession? session,
    List<ChatMessage>? messages,
    bool? isCameraOn,
    bool? isMuted,
  }) {
    return ConsultationActive(
      session: session ?? this.session,
      messages: messages ?? this.messages,
      isCameraOn: isCameraOn ?? this.isCameraOn,
      isMuted: isMuted ?? this.isMuted,
    );
  }
}

class ConsultationEnded extends ConsultationState {}

class ConsultationError extends ConsultationState {
  final String message;
  const ConsultationError(this.message);
}
