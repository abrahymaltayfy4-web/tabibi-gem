import 'package:flutter_bloc/flutter_bloc.dart';
import '../../data/datasources/consultation_remote_datasource.dart';
import '../../domain/entities/chat_message.dart';
import 'consultation_state.dart';

class ConsultationCubit extends Cubit<ConsultationState> {
  final ConsultationRemoteDataSource dataSource;

  ConsultationCubit({required this.dataSource}) : super(ConsultationInitial());

  Future<void> joinConsultation(int consultationId) async {
    emit(ConsultationLoading());
    try {
      final session = await dataSource.joinConsultation(consultationId);
      final messages = await dataSource.getMessages(session.conversationId);
      await dataSource.sendHeartbeat('InConsultation');

      emit(ConsultationActive(
        session: session,
        messages: messages,
      ));
    } catch (e) {
      emit(ConsultationError(e.toString()));
    }
  }

  Future<void> sendMessage(String text) async {
    if (state is! ConsultationActive) return;
    final currentState = state as ConsultationActive;

    final clientMsgId = 'msg_${DateTime.now().millisecondsSinceEpoch}';
    try {
      final message = await dataSource.sendMessage(currentState.session.conversationId, text, clientMsgId);
      final updatedMessages = [message, ...currentState.messages];
      emit(currentState.copyWith(messages: updatedMessages));
    } catch (e) {
      // Handle error gracefully
    }
  }

  void toggleCamera() {
    if (state is! ConsultationActive) return;
    final currentState = state as ConsultationActive;
    emit(currentState.copyWith(isCameraOn: !currentState.isCameraOn));
  }

  void toggleMute() {
    if (state is! ConsultationActive) return;
    final currentState = state as ConsultationActive;
    emit(currentState.copyWith(isMuted: !currentState.isMuted));
  }

  Future<void> endConsultation() async {
    if (state is! ConsultationActive) return;
    final currentState = state as ConsultationActive;

    try {
      await dataSource.endConsultation(currentState.session.consultationId);
      emit(ConsultationEnded());
    } catch (e) {
      emit(ConsultationEnded());
    }
  }
}
