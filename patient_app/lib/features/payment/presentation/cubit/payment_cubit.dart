import 'package:flutter_bloc/flutter_bloc.dart';
import '../../data/datasources/payment_remote_datasource.dart';
import 'payment_state.dart';

class PaymentCubit extends Cubit<PaymentState> {
  final PaymentRemoteDataSource dataSource;

  PaymentCubit({required this.dataSource}) : super(PaymentInitial());

  Future<void> checkout(int appointmentId) async {
    emit(PaymentLoading());
    try {
      final info = await dataSource.initiateCheckout(appointmentId);
      emit(PaymentCheckoutReady(info));
    } catch (e) {
      emit(PaymentError(e.toString()));
    }
  }

  Future<void> confirmPayment(String transactionReference) async {
    emit(PaymentLoading());
    try {
      await dataSource.completeSandboxCallback(transactionReference);
      emit(PaymentSuccess(transactionReference));
    } catch (e) {
      emit(PaymentError(e.toString()));
    }
  }
}
