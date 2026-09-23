import '../../domain/entities/payment_info.dart';

abstract class PaymentState {
  const PaymentState();
}

class PaymentInitial extends PaymentState {}

class PaymentLoading extends PaymentState {}

class PaymentCheckoutReady extends PaymentState {
  final PaymentInfo paymentInfo;
  const PaymentCheckoutReady(this.paymentInfo);
}

class PaymentSuccess extends PaymentState {
  final String transactionReference;
  const PaymentSuccess(this.transactionReference);
}

class PaymentError extends PaymentState {
  final String message;
  const PaymentError(this.message);
}
