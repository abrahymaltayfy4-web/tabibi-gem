import '../../../../core/network/api_client.dart';
import '../../domain/entities/payment_info.dart';

class PaymentRemoteDataSource {
  final ApiClient apiClient;

  PaymentRemoteDataSource({required this.apiClient});

  Future<PaymentInfo> initiateCheckout(int appointmentId) async {
    final response = await apiClient.post('/api/v1/payments/checkout/$appointmentId');
    final data = response.data['data'] as Map<String, dynamic>;
    return PaymentInfo.fromJson(data);
  }

  Future<void> completeSandboxCallback(String transactionReference) async {
    await apiClient.post('/api/v1/payments/sandbox-callback/$transactionReference');
  }
}
