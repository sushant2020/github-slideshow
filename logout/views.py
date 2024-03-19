# views.py

from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
import jwt

SECRET_KEY = 'Razor@0666!!!'  # Replace with your secret key
ALGORITHM = 'HS256'

@method_decorator(csrf_exempt, name='dispatch')
class LogoutAPI(View):
    def post(self, request, *args, **kwargs):
        try:
            # Get token from the frontend
            data = json.loads(request.body)
            token = data.get('token', '')

            # Decode the token to get the user data
            decoded_data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])

            # Perform additional actions if needed (e.g., logging, invalidating sessions)

            # Respond with success message
            response_data = {
                "success": True,
                "message": "User logged out successfully."
            }
            return JsonResponse(response_data, status=200)

        except jwt.ExpiredSignatureError:
            # Token has expired
            return JsonResponse({'success': False, 'message': 'Token has expired'}, status=401)

        except jwt.InvalidTokenError:
            # Invalid token
            return JsonResponse({'success': False, 'message': 'Invalid token'}, status=401)

        except Exception as e:
            # Handle other exceptions
            return JsonResponse({'success': False, 'message': str(e)}, status=500)
